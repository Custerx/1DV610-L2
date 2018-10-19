# 1DV610
# Status
## Criteria 1
92% on the automatic test. Failing hijacking session and changing cookiepassword.
I have not implemented an edit or delete option for the database (time issue).
Ive implemented the anti hiJacking session on login and cookielogin. 
Not to smart now when I looked at it. This is probably an easy fix 
because I have implemented the database code to handle this.

But I am running out of time and don't want to break something.

## Criteria 2
### UC5. View Account
#### Preconditions
A user is authenticated. UC1.
#### Main scenario
1. Starts when a user wants to check account details.
2. The system presents username and an edit account button.
#### Alternate Scenario
##### Preconditions
A user is authenticated. UC3.
2a. The system presents an EMPTY username and an edit account button.

### UC6. Edit Account
#### Preconditions
A user is authenticated. UC1. UC5.
#### Main scenario
1. User clicks on the edit account button.
2. The system presents a form for username, password, repeatpassword and an confirm button.
3. User fills in his new username and password and clicks on the confirm button.
4. The system presents the new username and an edit account button.
5. System presents a success message.
#### Alternate Scenario
2a. User fills in wrong username, password or repeatpassword.
    i. System presents an error message.
    ii. Step 2 in main scenario.

## Criteria 3
Refactored nearly my entire code that previously (L2) reached 96%.
I have been thinking about information expert, read it like a book and no string dependancies and such.

### Install
Rename Environment.php.default to Environment.php.

Edit Environment.php
1. Chose some random salt string.
2. Chose a file name and directory.

After that it should be ready to go. PS. Also place your password and username in Environment.php.
