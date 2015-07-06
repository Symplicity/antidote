<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Password reset</title>
        <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    </head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    <table style="background-color:#f2f2f2" border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="#f2f2f2">
    <tbody>
    <tr>
        <td style="background-color:#f2f2f2" align="center" bgcolor="#f2f2f2">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tbody>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; background-color:#f2f2f2" align="center" bgcolor="#f2f2f2">
            <table border="0" cellspacing="0" cellpadding="0" width="600" align="center">
                <tbody>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; background-color:#f2f2f2" align="center" bgcolor="#ffffff">
            <table border="0" cellspacing="0" cellpadding="0" width="600" align="center">
                <tbody>
                <tr>
                    <td style="padding:40px 65px 50px; text-align: left;">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tbody>
                            <tr>
                                <p><a href="<?php echo $site_url; ?></p>"><img src="<?php echo $site_url; ?>/images/logo-purp.png" border="0" alt="Antidote Logo" width="141" /></a></p>
                                <td style="font-family: 'Lato', sans-serif; font-size: 17px; color: #333333; text-align: left; padding-bottom: 20px; line-height:24px;">
                                    <p style="color:#3F51B5; font-weight:700">Hi, <?php echo $email; ?></p>
                                    <p>You have recently requested a password reset.</p>
                                    <p>To change your Antidote password, click <span style="color:#3F51B5; font-weight:700"><a href="<?php echo $change_password_link; ?>">here</a></span> or paste the following into your browser <span style="color:#3F51B5; font-weight:700"><?php echo $change_password_link; ?></span></p>
                                    <p>The link will expire in <?php echo $link_expiration_time; ?>, so be sure to use it the right away.</p>
                                    <p style="color:#3F51B5; font-weight:700"><br>The Antidote Team</b></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; background-color:#bbb" align="center" bgcolor="#333333">
            <table border="0" cellspacing="0" cellpadding="0" width="600" align="center">
                <tbody>
                <tr>
                    <td style="padding:20px 20px 20px 40px; vertical-align:middle" width="101" align="left"><a href="http://www.symplicity.com"></a></td>
                    <td align="left">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tbody>
                            <tr>
                                <td width="20%"
                                    style="font-family:'Helvetica Neue', Helvetica, Arial, sans serif; font-size:11px; text-decoration: none; color:#333; font-weight:700;">
                                    <a style="font-family:'Helvetica Neue', Helvetica, Arial, sans serif; font-size:11px; text-decoration: none; color:#333"
                                       href="<?php echo $site_url; ?>">Antidote</a></td>
                                <td width="20%"><a
                                        style="font-family:'Helvetica Neue', Helvetica, Arial, sans serif; font-size:11px; text-decoration: none; color:#333; font-weight:700;"
                                        href="<?php echo $site_url; ?>/privacy">Privacy Policy</a></td>
                                <td width="20%"><a
                                        style="font-family:'Helvetica Neue', Helvetica, Arial, sans serif; font-size:11px; text-decoration: none; color:#333; font-weight:700;"
                                        href="<?php echo $site_url; ?>/about">About</a></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; background-color:#333" align="center" bgcolor="#292929">
            <table border="0" cellspacing="0" cellpadding="0" width="600" align="center">
                <tbody>
                <tr>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
    </table>
    </td>
    </tr>
    </tbody>
    </table>
    </body>
</html>
