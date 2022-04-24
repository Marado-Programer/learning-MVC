<?php

/**
 * 
 */

class HomeModel extends MainModel
{
    public function getUserAssociations()
    {
        $associations = $this->db->query(
            $this->db->createQuery('SELECT * FROM `usersAssociations` WHERE `user` = ?;'),
            [UserSession::getUser()->getID()]
        );

        if (!$associations)
            return;

        foreach ($associations->fetchAll(PDO::FETCH_ASSOC) as $association) {
            $this->controller->userAssociations->add($this->instanceAssociationByID($association['association']));
        }
    }

    private function instanceAssociationByID(int $id)
    {
        $association = $this->db->query(
            $this->db->createQuery("SELECT * FROM `associationWPresident` WHERE `id` = ?;"),
            [$id]
        );

        if (!$association)
            return;

        $association = $association->fetch(PDO::FETCH_ASSOC);

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
        $extends = 'User';
        try {
            $user = $this->db->query(
                $this->db->createQuery('SELECT * FROM `users` WHERE `id` = ?;'),
                [$id]
            );

            if (!$user)
                throw new Exception('Error fiding user');

            $user = $user->fetch(PDO::FETCH_ASSOC);

            $userRoles = $this->db->query(
                $this->db->createQuery("SELECT `role` FROM `usersAssociations` WHERE `user` = ?;"),
                [$user['id']]
            )->fetchAll(PDO::FETCH_ASSOC);

            if (count($userRoles) > 0) {
                $extends = 'Partner';
                foreach ($userRoles as $role) 
                    if (
                        UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                            $role['role'],
                            PermissionsManager::AP_PRESIDENT,
                            false
                        )
                    ) {
                        $extends = 'President';
                        break;
                    }
            }

            return new $extends(
                $user['username'],
                null,
                $user['realName'],
                $user['email'],
                $user['telephone'],
                $user['wallet'] ?? 0,
                $user['permissions'],
                false,
                $id
            );
        } catch (Exception $e) {
            die($e);
        }
    }

    public function createAssociation()
    {
        if (
            !UsersManager::getTools()->getPermissionsManager()->checkUserPermissions(
                $this->controller->userSession->user,
                PermissionsManager::P_CREATE_ASSOCIATIONS,
                false
            )
        )
            return;

        $association = $_POST['create'];

        unset($_POST['create']);

        if (
            $this->db->query(
                'SELECT * FROM `associations` WHERE `name` = ?;',
                [
                    $association['name'],
                ]
            )->fetchAll()
        )
            return;

        if (!$association['address'])
            unset($association['address']);

        if ($association['phone'] == 'yours' && !isset($this->controller->userSession->user->telephone))
            return;

        if ($association['phone'] == 'new' && !isset($association['int'], $association['number']))
            return;

        $association['telephone'] = $association['phone'] == 'new'
            ? '+' . $association['int'] . ' ' . $association['number']
            : $this->controller->userSession->user->telephone;

        unset($association['phone'], $association['int'], $association['number']);

        $this->db->insert('associations', array_merge(
            $association,
            [
                'president' => $this->controller->userSession->user->id
            ]
        ));
    }

    public function createNews()
    {
        if (!UserSession::getUser() instanceof Partner)
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
        $paragraphs = "";
        foreach (explode("\n\r", $news['article']) as $paragraph)
            $paragraphs .= '<p>' . trim($paragraph) . '</p>';

        // 65535 it's the max bytes that the MySQL TEXT data type can handle
        if (strlen($paragraphs) > 65_535) {
            $foundError = true;
            $errors[] = 'The article was too big, maxlength it\'s 65.535 bytes.';
            $errors[] = 'One good solution it\'s to the news in two or more, or use an external tool.';
            $news['article'] = substr($news['article'], 0, 65_535 - (strlen($paragraphs) - strlen($news['article'])));
        } elseif (strlen($paragraphs) <= 0) {
            $foundError = true;
            $errors[] = 'The article it\'s too much short.';
            $errors[] = 'Write something more.';
        }

        $news['article'] = $paragraphs;
        unset($paragraphs);

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
            $this->instanceAssociationByID($news['association']),
            $news['title'],
            $news['image'],
            $news['article']
        );
    }

    public function payQuota(int $associationID)
    {
        $association = $this->instanceAssociationByID($associationID);
        
        UserSession::getUser()->payQuota($association);
    }
}
