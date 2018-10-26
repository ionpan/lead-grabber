<!DOCTYPE html>
<html>
    <head data-locale="en">
        <meta http-equiv="content-type">
        <meta content="text/html">
        <meta charset="UTF-8">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <title>Contacts</title>
    </head>
    <body>
        <h3>CONTACTS</h3>
        <table>
            <tr>
                <th>Registration Datetime</th>
                <th>Full Name</th> 
                <th>Email</th>
                <th>Opt-in</th> 
            </tr>
            <?php
            $dbh = require('database.php');
            try {
                $sth = $dbh->prepare('SELECT * FROM `contact` ORDER BY `registration_datetime` DESC');
                $sth->execute([]);
                $contacts = $sth->fetchAll();
                foreach ($contacts as $contact) {
                    ?>
                    <tr id="<?= $contact->id ?>">
                        <td><?= $contact->registration_datetime ?></td>
                        <td><?= $contact->full_name ?></td>
                        <td><?= $contact->email ?></td>
                        <td><?= $contact->optin ?></td>
                    </tr>
                    <?php
                }
            } catch (Exception $e) {
                ?>
                <tr colspan="4">There was an internal server error.<tr>
                    <?php
                }
                ?>
        </table>
        <script src="/lead-grabber/assets/view.js"></script>
    </body>
</html>
