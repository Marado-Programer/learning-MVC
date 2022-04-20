<?php

/**
 * 
 */

class AssociationsAdmniModel extends MainModel
{
    public function getAssociationByNickname($nickname)
    {
        $association = $this->db->query("
            SELECT * FROM `associationWPresident`
            WHERE `nickname` = ?",
            [$nickname]
        );

        if (!$association)
            return;

        return $this->instanceAssociation($association->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceAssociation(array $association)
    {
        if (($user = clone UserSession::getUser())->id != $association['president'])
            $user = $this->instanceUserByID($association['president']);

        return $user->initAssociation(
            $association['id'],
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
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
        $role = $this->db->query("SELECT * FROM `usersAssociations` WHERE (`association` = {$association->id}) AND (`user` = {$user->id});");
        
        if (!$role)
            return 0;

        $role = $role->fetch(PDO::FETCH_ASSOC);

        if (!isset($role['role']))
            return 0;

        return $role['role'] ?? 0;
    }

    public function createNews(Association $association)
    {
        if (!UsersManager::getPermissionsManager()->checkPermissions(
            $this->userAdmniPermissions(UserSession::getUser(), $association),
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

        // Making paragraphs from the article text
        $paragraphs = "\0";
        foreach (explode("\n\r", $news['article']) as $paragraph)
            $paragraphs .= '<p>' . trim($paragraph) . '</p>';
        $news['article'] = $paragraphs;
        unset($paragraphs);

        // 65535 it's the max bytes that the MySQL TEXT data type can handle
        if (strlen($news['article']) > 65_535) {
            $foundError = true;
            $errors[] = 'The article was too big, maxlength it\'s 65.535 bytes.';
            $errors[] = 'One good solution it\'s to the news in two or more.';
            $news['article'] = substr($news['article'], 0, 65_535);
        } elseif (strlen($news['article']) <= 0) {
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

        $user = UserSession::getUser();
        if (!method_exists($user, 'createNews'))
            die('No permissions');

        $user->createNews(
            $association,
            $news['title'],
            $news['image'],
            $news['article']
        );
    }

    public function createEvent(Association $association)
    {
        if (!UsersManager::getPermissionsManager()->checkPermissions(
            $this->userAdmniPermissions(UserSession::getUser(), $association),
            PermissionsManager::AP_CREATE_EVENTS,
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

        if (!isset($_POST['event'])) {
            $foundError = true;
            $errors[] = 'There\'s nothing to create.';
        }

        $event = $_POST['event'] ?? [];

        if (empty($event)
            || empty($event['title'])
            || empty($event['description'])
            || empty($event['endDate'])
        ) {
            $foundError=true;
            $errors[] = 'Be sure that all the fields (title and description) have input.';
        }

        if (isset($event['title']))
            $event['title'] = strip_tags($event['title']);

        if (strlen($event['title']) > 80) {
            $foundError = true;
            $errors[] = 'The title was too big, maxlength it\'s 80 bytes.';
            $errors[] = 'Please revise your title.';
            $event['title'] = substr($event['title'], 0, 80);
        } elseif (strlen($event['title']) <= 0) {
            $foundError = true;
            $errors[] = 'The title it\'s too much short.';
            $errors[] = 'Please revise your title.';
        }

        // the article can use only the tags above
        if (isset($event['description']))
            $event['description'] = strip_tags($event['description']);

        if (strlen($event['description']) > 280) {
            $foundError = true;
            $errors[] = 'The article was too big, maxlength it\'s 280 bytes.';
            $event['description'] = substr($event['description'], 0, 280);
        } elseif (strlen($event['description']) <= 0) {
            $foundError = true;
            $errors[] = 'The article it\'s too much short.';
            $errors[] = 'Write something more.';
        }

        $event['endDate'] = DateTime::createFromFormat('Y-m-d\TH:i', $event['endDate']);

        if ($event['endDate'] < new DateTime()) {
            $foundError = true;
            $errors[] = 'The date has already passed.';
        }

        // And there it is. If found error during the function return null and
        // the errors and corrected input
        $_SESSION['event'] = serialize($event);
        if ($foundError) {
            $_SESSION['news-errors'] = $errors;
            unset($event, $foundError, $errors);
            return;
        }

        $association->createEvent($event['title'], $event['description'], $event['endDate']);

        unset($event);

        $_SESSION['event-created'] = 'An event was created.';

        unset($foundError, $errors, $_SESSION['event']);
    }

    public function createImage(Association $association)
    {
        if (!UsersManager::getPermissionsManager()->checkPermissions(
            $this->userAdmniPermissions(UserSession::getUser(), $association),
            PermissionsManager::AP_CREATE_IMAGES,
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

        if (!isset($_POST['image']) || !isset($_FILES['image-image'])) {
            $foundError = true;
            $errors[] = 'There\'s nothing to create.';
        }

        $image = $_POST['image'] ?? [];
        $image['image'] = $_FILES['image-image'] ?? [];

        if (empty($image)
            || empty($image['title'])
            || empty($image['image'])
        ) {
            $foundError=true;
            $errors[] = 'Be sure that all the fields (title, image) have input.';
        }

        if (isset($image['title']))
            $image['title'] = strip_tags($image['title']);

        if (strlen($image['title']) > 80) {
            $foundError = true;
            $errors[] = 'The title was too big, maxlength it\'s 80 bytes.';
            $errors[] = 'Please revise your title.';
            $image['title'] = substr($image['title'], 0, 80);
        } elseif (strlen($image['title']) <= 0) {
            $foundError = true;
            $errors[] = 'The title it\'s too much short.';
            $errors[] = 'Please revise your title.';
        }

        if ($image['image']['tmp_name'] == 'none' || $image['image']['size'] <= 0) {
            $foundError = true;
            $errors[] = 'No image found.';
        }

        // The image size it's in bytes
        if ($image['image']['size']/(1024**2) > 2) {
            $foundError = true;
            $errors[] = 'The image found it\'t too big.';
        }

        if (!preg_match("/^image\//", $image['image']['type'])) {
            $foundError = true;
            $errors[] = 'Not supported file type';
        }

        if ($image['image']['error'] == UPLOAD_ERR_OK) {
                $image['image']['name'] = md5(mt_rand(1, 10000).$image['image']['name'])
                    . substr(
                        $image['image']['name'],
                        strpos(
                            $image['image']['name'],
                            '.'
                        )
                    );
        } else {
            switch ($image['image']['error']) {
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

        // And there it is. If found error during the function return null and
        // the errors and corrected input
        $_SESSION['image'] = serialize($image);
        if ($foundError) {
            $_SESSION['image-errors'] = $errors;
            unset($image, $foundError, $errors);
            return;
        }

        $association->createImage(
            $image['title'],
            $image['image'],
        );
    }

}

