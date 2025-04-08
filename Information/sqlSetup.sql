CREATE TABLE Users ( 

    userId INT NOT NULL AUTO_INCREMENT, -- user ID that increments 
    firstName VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- hashed

    primary key (id) -- no two users can have the same ID
) Engine = InnoDB; 

CREATE TABLE Contacts (

    id INT NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phoneNumber VARCHAR(50) NOT NULL,
    address VARCHAR(50), -- optional contact location field

    userId INT NOT NULL, -- matches users to their contacts
    primary key (id) -- no two contacts can have the same ID
    foreign key (userId) references Users(id) on delete cascade -- if user is del, all contacts are del too
) Engine = InnoDB;

