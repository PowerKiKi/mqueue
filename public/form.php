<html><body>
        <form method="post" action=""><fieldset>
                <legend>OpenID Login</legend>
                <input type="text" name="openid_identifier">
                <input type="submit" name="openid_action" value="login">
            </fieldset></form>
        <?php
        require('../application/debug.php');
        require('index.php');

        v($_GET, $_POST);

        $sreg = new Zend_OpenId_Extension_Sreg(array(
            'nickname' => true,
            'email' => true,
            'fullname' => false
            ), null, 1.1);

        if (@$_POST['openid_identifier']) {
            $consumer = new Zend_OpenId_Consumer();
            if (!$consumer->login($_POST['openid_identifier'], 'http://localhost/mqueue/public/form.php', null, $sreg)) {
                echo $consumer->getError();
                die("OpenID login failed.");
            }
        } elseif (count($_GET)) {
            $consumer = new Zend_OpenId_Consumer();
            if ($consumer->verify($_GET, $id, $sreg)) {
                echo "VALID " . htmlspecialchars($id);

                $data = $sreg->getProperties();
                v($data);
            } else {
                echo "INVALID " . htmlspecialchars($id);
            }
            v($id);
        }
