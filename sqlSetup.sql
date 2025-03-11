CREATE TABLE Users ( 

    id INT NOT NULL AUTO_INCREMENT, -- user ID that increments 
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- hashed

    primary key (id) -- no two users can have the same ID
) Engine = InnoDB; 

CREATE TABLE Contacts (

    id INT NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    location VARCHAR(50), -- optional contact location field

    userId INT NOT NULL, -- matches users to their contacts
    primary key (id) -- no two contacts can have the same ID
    foreign key (userId) references Users(id) on delete cascade -- if user is del, all contacts are del too
) Engine = InnoDB;

