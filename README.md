# Project Database & SQL Queries

Project Description 
This web application is a gym management system designed for users to sign up for classes and manage their subscriptions. The users are gym members, instructors, and administrators. The instructors can create and manage class schedules and track attendance. The administrators can manage memberships, trainers, and class schedules.

## Requirements
Instructors can create and change class schedules
Instructors can view and track class attendance
Members can view and update their personal information (e.g. name, address, email)
Members can sign up for any classes using their membership ID
Members can drop out of any classes (e.g. delete their name)
Members can view and update their membership status
Administrators can modify class schedules and instructor information, and manage gym memberships

## Business Rules
A class must have at least one instructor
Instructors can only manage classes they are assigned to
Each class can have at most thirty members
Members can sign up for any given classes
Members can only sign up for a class if there are open spots
Administrators can modify all classes and memberships


## Table structures

<img width="924" alt="image" src="https://github.com/user-attachments/assets/0040293a-aee4-429c-bd46-8b402c67427b">

## Class table

<img width="725" alt="image" src="https://github.com/user-attachments/assets/8d909736-8e3c-4452-abf4-054eb4ac9083">

## Instructors Table

<img width="773" alt="image" src="https://github.com/user-attachments/assets/586483ec-c698-4718-a070-4b0af06968b2">

## Members table

<img width="1038" alt="image" src="https://github.com/user-attachments/assets/dcf7470e-da7d-409b-9bba-269bd3cfeedf">

## Membership table

<img width="496" alt="image" src="https://github.com/user-attachments/assets/4a331144-1f39-4739-a611-ccb8d5e840cd"> 

## Queries:

CREATE:
CREATE TABLE Members (
    member_id INT PRIMARY KEY,
    membership_id INT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email TEXT, 
    phone VARCHAR(15), 
    city VARCHAR(50),
    state VARCHAR(50),
    status VARCHAR(20) NOT NULL, 
    FOREIGN KEY (membership_id) REFERENCES Membership(membership_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

This query creates a Members Table with the following attributes: member_id(PK, integer), membership_id(FK, integer), first_name(varchar, not null), last_name(varchar, not null), email(text), phone(varchar), city(varchar), state(varchar), status(varchar, not null). A foreign key constraint is applied to prevent integrity violations between the Members and Memberships. This is used for setting up the Members entity and its attributes, and it will be required for our project.

INSERT:
INSERT INTO Membership (membership_id, price, type) VALUES 
(11, 30.00, 'Senior Monthly'), 

This query inserts a new membership type into the Membership table. It could be used when the administrator introduces a new membership plan. 

UPDATE:
UPDATE Class
SET capacity = 15
WHERE class_name = ‘Yoga’;

This query updates the yoga class size by setting its capacity to 15. It could be used when the instructor or administrator decides to limit how many people can take the class at a given time.






SELECT using JOIN:
SELECT Members.first_name, Members.last_name, Class.class_name
FROM Members
JOIN Takes ON Members.member_id = Takes.member_id
JOIN Class ON Takes.member_id = Class.class_id;

This query displays which classes each member is enrolled in. It could be used to generate a schedule for a member.

Aggregate Function:
SELECT AVG(price) AS avg_price
FROM Membership;

This query calculates the average price for all membership types. It could be used to evaluate pricing strategies.

DELETE:
DELETE FROM Member
WHERE status = ‘inactive’;

This query deletes all inactive members and their existing data from the Members table. This could be used to clean up the database to prevent any confusion.


Trigger/Function/Procedure (more stuff may be added later on)
Triggers for “Instructors can only manage classes they are assigned to”:
CREATE TRIGGER UnauthorizedClassUpdate
BEFORE UPDATE ON Class
FOR EACH ROW BEGIN
	DECLARE authorizedInstructor INT
	SET authorizedInstructor = (SELECT instructor_id FROM Class
					WHERE class_id = NEW.class_id);
	IF authorizedInstructor != NEW.instructor_id THEN
		SIGNAL SQLSTATE ‘45000’
SET Message_Text = ‘Instructors can only manage the classes they are assigned to’;
	END IF;
END;







