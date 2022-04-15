<?php

/**
 * 
 */

class AssociationsAdmniModel extends MainModel
{
    public function getAssociationByNickname($nickname)
    {
        $association = $this->db->query("SELECT * FROM `associations` WHERE `nickname` = '$nickname';");

        if (!$association)
            return;

        return $this->instanceAssociation($association->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceAssociation(array $association)
    {
        return new Association(
            $association['id'],
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
            $this->instanceUserByID($association['president'])
        );
    }

    private function instanceUserByID(int $id)
    {
        $user = $this->db->query("SELECT * FROM `users` WHERE `id` = $id;");

        if (!$user)
            return;

        $user = $user->fetch(PDO::FETCH_ASSOC);

        return new User(
            $user['username'],
            null,
            $user['realName'],
            $user['email'],
            $user['telephone'],
            $user['permissions'],
            false,
            $id
        );
    }

    public function userAdmniPermissions(User $user, Association $association): int
    {
        $role = $this->db->query("SELECT * FROM `usersAssociations` WHERE (`associationID` = {$association->id}) AND (`userID` = {$user->id});");
        
        if (!$role)
            return 0;

        $role = $role->fetch(PDO::FETCH_ASSOC);

        if (!isset($role['role']))
            return 0;

        return $role['role'] ?? 0;
    }

    public function createNews(User $user, Association $association)
    {
        if (!UsersManager::getPermissionsManager()->checkPermissions(
            $this->userAdmniPermissions($user, $association),
            PermissionsManager::AP_CREATE_NEWS,
            false
        ))
            return;

        /**
        * We have this control variables because we want to show the partner the
        * rectified way to write their things.
        *
        * We will correct the partner input because we need it, if there's any
        * error found the variable will become true and after all the
        * corrections we test it, if it's true stop the function before creating
        * a corrupt news, show the corrected version to the partner and point out
        * errors, else we keep creating the news.
        */
        $foundError = false;
        $errors = [];

        if (!isset($_POST['create']) || !isset($_FILES['create-image'])) {
            $foundError = true;
            $errors[] = 'There\'s nothing to create.';
        }

        $news = $_POST['create'] ?? [];
        $news['image'] = $_FILES['create-image'] ?? [];

        if (empty($news)
            || empty($news['title'])
            || empty($news['image'])
            || empty($news['article'])
        ) {
            $foundError=true;
            $errors[] = 'Be sure that all the fields (title, image and article) have input.';
        }

        if (isset($news['title']))
            $news['title'] = strip_tags($news['title']);

        if (strlen($news['title']) > 80) {
            $foundError = true;
            $errors[] = 'The title was too big, maxlength it\'s 80 bytes.';
            $errors[] = 'Please revise your title.';
            $news['title'] = substr($news['title'], 0, 80);
        } elseif (strlen($news['title']) <= 0) {
            $foundError = true;
            $errors[] = 'The title it\'s too much short.';
            $errors[] = 'Please revise your title.';
        }

        if ($news['image']['tmp_name'] == 'none' || $news['image']['size'] <= 0) {
            $foundError = true;
            $errors[] = 'No image found.';
        }

        // The image size it's in bytes
        if ($news['image']['size']/(1024**2) > 2) {
            $foundError = true;
            $errors[] = 'The image found it\'t too big.';
        }

        if (!preg_match("/^image\//", $news['image']['type'])) {
            $foundError = true;
            $errors[] = 'Not supported file type';
        }

        if ($news['image']['error'] == UPLOAD_ERR_OK) {
                $news['image']['name'] = md5(mt_rand(1, 10000).$news['image']['name'])
                    . substr(
                        $news['image']['name'],
                        strpos(
                            $news['image']['name'],
                            '.'
                        )
                    );
        } else {
            switch ($news['image']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $foundError = true;
                    $errors[] = 'The image found it\'t too big.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                    $foundError = true;
                    $errors[] = 'File didn\'t upload correctly.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $foundError = true;
                    $errors[] = 'Invalid image file.';
                    break;
                default:
                    $foundError = true;
                    $errors[] = 'Unknown error on the image upload.';
                    break;
            }
        }

        // list of text-level semantics HTML 5 tags
        $premittedTags = [
            'a',
            'em',
            'strong',
            'small',
            's',
            'cite',
            'q',
            'dfn',
            'abbr',
            'ruby',
            'rt',
            'rp',
            'data',
            'time',
            'code',
            'var',
            'samp',
            'kbd',
            'sub',
            'sup',
            'i',
            'b',
            'u',
            'mark',
            'bdi',
            'bdo',
            'br',
            'wbr'
        ];

        // the article can use only the tags above
        if (isset($news['article']))
            $news['article'] = strip_tags($news['article'], $premittedTags);

        // 65535 it's the max bytes that the MySQL TEXT data type can handle
        if (strlen($news['title']) > 65_535) {
            $foundError = true;
            $errors[] = 'The article was too big, maxlength it\'s 65.535 bytes.';
            $errors[] = 'One good solution it\'s to the news in two or more.';
            $news['title'] = substr($news['title'], 0, 80);
        } elseif (strlen($news['title']) <= 0) {
            $foundError = true;
            $errors[] = 'The article it\'s too much short.';
            $errors[] = 'Write something more.';
        }

        // And there it is. If found error during the function return null and
        // the errors and corrected input
        $_SESSION['news'] = serialize($news);
        if ($foundError) {
            $_SESSION['news-errors'] = $errors;
            unset($news, $foundError, $errors);
            return;
        }

        // Making paragraphs from the article text
        $paragraphs = "\0";
        foreach (explode("\n\r", $news['article']) as $paragraph)
            $paragraphs .= '<p>' . trim($paragraph) . '</p>';
        $news['article'] = $paragraphs;
        unset($paragraphs);

        $createdNews = $this->db->insert(
            'news',
            [
                'association' => $association->id,
                'author' => $user->id,
                'title' => $news['title'],
                'image' => $news['image']['name'],
                'article' => $news['article'],
                'publishTime' => ($now = new Datetime())->getTimestamp(),
                'lastEditTime' => $now->getTimestamp()
            ]
        );

        unset($now);

        if (!$createdNews) {
            $errors[] = "Failed to create news";
            $_SESSION['news-errors'] = $errors;
            unset($news, $foundError, $errors);
            return;
        }

        if (!file_exists(UPLOAD_PATH))
            mkdir(UPLOAD_PATH, 0755, true);

        if (!move_uploaded_file($news['image']['tmp_name'], UPLOAD_PATH . '/' . $news['image']['name'])) {
            $errors[] = "Failed uploading file";
            $_SESSION['news-errors'] = $errors;
            unset($news, $foundError, $errors);
            return;
        }

        $_SESSION['news-created'] = 'A news was created.';

        unset($news, $foundError, $errors, $_SESSION['news']);
    }
}

