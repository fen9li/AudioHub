## Versions
**Version 1.0.1**

## Description
- [x] Fresh install of Laravel Framework 5.5.40.
- [x] Set up Laravel Dusk Browser test.
- [x] Change website title.
- [x] Add basic auth - test register.
- [x] Add basic auth - test login.
- [x] Add basic auth - test reset password.
- [x] Custom reset password email.

- [x] Create UserRegisteredEvent
- [x] Create SendEmailVerificationNotification listener against UserRegisteredEvent
- [x]  Create EmailVerificationNotification and its associate view blade 'EmailVerificationNotification'
- [x] Create SaveEmailVerificationToken listener against UserRegisteredEvent, which acts before SendEmailVerificationNotification listener
- [x] Create EmailVerificationTrait to support UserRegisteredEvent listeners
