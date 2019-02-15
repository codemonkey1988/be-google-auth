# Google oAuth for TYPO3 backend
This extension enables Google oAuth authentication for TYPO3 backend users.

## Motivation
Companies especially agencies needs to manage multiple TYPO3 backend users 
for multiple instances. This can be a really annoying and error-prone process.
This extension enables TYPO3 backend login with google accounts, that might get
managed by a G Suite Administrator account. So there is a central user management.

## Installation
This extension can currently only be installed by composer.

```
composer install codemonkey1988/be-google-auth
```

## Configuration

To use this extension, you must create a Google OAuth client ID. 

### Create Google OAuth client ID

First you need a create OAuth credentials:

1. Login to [https://console.cloud.google.com/](https://console.cloud.google.com/)
2. Navigate to **APIs & Services** using the burger menu on the top left edge
3. Navigate to **Credentials** in the sub-navigation.
4. Create new credentials using the **Create credentials** button and select **OAuth client ID**
5. Choose **Web application** as application type
6. Enter a name for the credentials
7. Enter the URL from your TYPO3 installation into the **Authorized JavaScript Origins** field
8. Leave the **Authorized redirect URLs** field blank

Second you need a setup an OAuth consent screen:

1. Login to [https://console.cloud.google.com/](https://console.cloud.google.com/)
2. Navigate to **APIs & Services** using the burger menu on the top left edge
3. Navigate to **Credentials** in the sub-navigation.
4. Switch to the Tab **OAuth consent screen**
5. Set **Application type** to Internal
6. Enter an Application name and optionally upload a logo
7. Add the following items to **Scopes for Google APIs**: `email, profile, openid`

You are not ready to go. To improve security you can also set **Authorized domains** 
according to your TYPO3 installation.

### Setup the extension

After installing the extension, you need to do some setup (it will be quick 😉)

1. Go to the extension configuration<br>
_(In TYPO3 v8 it is available using the ⚙️ button in the **Extensions** module, 
in TYPO3 v9 use go to **Settings** module and choose **Extension Configuration**)_
2. Enter you Google OAuth client ID into the corresponding field and save the configuration
3. Create or edit a backend user and add the email address from the users Google account into the email field

This user can now login using his Google account. The user will have the same privileges as logging
in with username and password.

### Setup G Suite usage

This extension also supports G Suite accounts. Using G Suite setup allows all users belonging to a
configured organisation to login using their google accounts without creating a backend user first.
When there is not backend user, a new user will be automatically created during the first login process.

The setup is also done in the extension configuration (see _Setup the extension_).
The following part will describe the available settings.

**Enable Google G Suite features**<br>
Enables the G Suite features.

**Organisations**<br>
A list of G Suite organisations (domains) that should get access to the TYPO3 system. 
**Note that every user that belongs to one of the organisations will have access to the TYPO3 backend.**

**Create admin users by default**
Every new user that logs in to TYPO3 will automatically get admin privileges. **USE WITH CARE!**

**Create admin users by email address in file**<br>
You can specify a path to a text file that contains email addresses (one each line)
All new users matching one of the email addresses in this file will get admin privileges. 
All other accounts will be normal TYPO3 backend users. (See _Backend user group uids_)<br>
You can use a local path (also with EXT: prefix) or a url.

**Backend user group uids**
A list of UIDs for backend user groups. This backend user groups will get assigned to all new users that 
do not have admin privileges.

## Found an issue?

You can create new issues at https://github.com/codemonkey1988/be-google-auth/issues.<br>
If you found a **security issue** please contact me personally using one of the following methods:
* Twitter: [@codemonkey1988](https://twitter.com/Codemonkey1988)
* TYPO3 Slack: timschreiner
* Email: [dev@tim-schreiner.de](dev@tim-schreiner.de)

## Special Thanks

A special thanks goes to [Georg Ringer](https://montagmorgen.at/) who inspired me with this idea.
This extension is based on his extension [google_signin](https://github.com/georgringer/google_signin/).
