# 1DV610
# Status
## Criteria 1
92% on the automatic test. Failing hijacking session and changing cookiepassword.
I have not implemented an edit option for the database (time issue).
Ive implemented the anti hiJacking session on login and cookielogin. 
Not to smart now when I looked at it. This is probably an easy fix 
because I have implemented the database code to handle this.

But I am running out of time and don't want to break something.

## Criteria 2

### UC5. View Account
#### Preconditions
A user is authenticated. UC1, UC3.
#### Main scenario
1. Starts when a user wants to check account details.
2. The system presents a view account choice.
3. User tells the system he/she wants to view account.
4. The system presents the account details.

#### Alternate Scenario
4a. System could not find account details.
    i. System presents an error message.
    ii. Step 2 in main scenario.

### UC6. Forgot Password
#### Main scenario

## Criteria 3
Refactored nearly my entire code that reached 96%.
I have been thinking about information expert, read it like a book and no string dependancies and such.

What is implemented?

Describe how to install?
