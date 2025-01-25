CREATE DATABASE IF NOT EXISTS nwfh;
USE nwfh;

CREATE TABLE IF NOT EXISTS Users (
    UID      INT                   NOT NULL AUTO_INCREMENT,
    Username VARCHAR(50)           NOT NULL UNIQUE,
    Password VARCHAR(50)           NOT NULL,
    Role     ENUM('User', 'Admin') NOT NULL,
    PRIMARY KEY (UID)
);

CREATE TABLE IF NOT EXISTS Cookies (
    UID     INT NOT NULL AUTO_INCREMENT,
    Token   CHAR(32) NOT NULL,
    Expires DATETIME,
    PRIMARY KEY (UID, Token),
    FOREIGN KEY (UID) REFERENCES Users(UID)
);

CREATE TABLE IF NOT EXISTS Webpages (
    WID          INT          NOT NULL AUTO_INCREMENT,
    Path         VARCHAR(512) NOT NULL,
    URL          VARCHAR(512) NOT NULL,
    Date         DATETIME     NOT NULL,
    Visits       INT          NOT NULL,
    RequesterUID INT          NOT NULL,
    FaviconPath  VARCHAR(512),
    PRIMARY KEY (WID),
    FOREIGN KEY (RequesterUID) REFERENCES Users(UID)
);

CREATE TABLE IF NOT EXISTS ArchiveLists (
    LID         INT         NOT NULL AUTO_INCREMENT,
    AuthorUID   INT         NOT NULL,
    Name        VARCHAR(64) NOT NULL,
    Description VARCHAR(255),
    PRIMARY KEY (LID),
    FOREIGN KEY (AuthorUID) REFERENCES Users(UID)
);

CREATE TABLE IF NOT EXISTS ArchiveListsWebpages (
    WID      INT NOT NULL,
    LID      INT NOT NULL,
    Position INT NOT NULL,
    PRIMARY KEY (WID, LID)
);
