## Versions
**Version 1.0.2**

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

- [x] Create custom exceptions
- [x] Create verify email feature and its belonging test
- [x] Add verify email route
- [x] Redirect user to '/' after register
- [x] Redirect user to '/home' after verify email
- [x] Update home page to display session 'success' message
- [x] Custom welcome page

- [x] Create EmailVerificationMiddleware and its accompany tests 
- [x] Hookup email.verification Middleware and LoginController login route
- [x] Update old dusk tests to fix the broken tests
- [x] Update welcome blade to display session message
- [x] Ensure all feature tests and dusk tests pass successfully
- [x] Queue email notification successfully
