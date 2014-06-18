
-----------------------------------------------------------------------
-- backup
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [backup];

CREATE TABLE [backup]
(
    [id] INTEGER NOT NULL PRIMARY KEY,
    [siteId] INTEGER,
    [projectId] INTEGER,
    [component] VARCHAR(15),
    [path] VARCHAR(255),
    [createdOn] TIMESTAMP,
    [updatedOn] TIMESTAMP
);

-- SQLite does not support foreign keys; this is just for reference
-- FOREIGN KEY ([siteId]) REFERENCES site ([id])

-----------------------------------------------------------------------
-- environment
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [environment];

CREATE TABLE [environment]
(
    [id] INTEGER NOT NULL PRIMARY KEY,
    [siteId] INTEGER,
    [name] VARCHAR(255) NOT NULL,
    [host] VARCHAR(255),
    [username] VARCHAR(255),
    [branch] VARCHAR(255),
    [createdOn] TIMESTAMP,
    [updatedOn] TIMESTAMP
);

-- SQLite does not support foreign keys; this is just for reference
-- FOREIGN KEY ([siteId]) REFERENCES site ([id])

-----------------------------------------------------------------------
-- project
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [project];

CREATE TABLE [project]
(
    [id] INTEGER NOT NULL PRIMARY KEY,
    [name] VARCHAR(255) NOT NULL,
    [uuid] VARCHAR(255),
    [siteId] INTEGER,
    [hostname] VARCHAR(255),
    [username] VARCHAR(255),
    [sshPort] INTEGER,
    [codePath] VARCHAR(255),
    [filesPath] VARCHAR(255),
    [databaseHost] VARCHAR(255),
    [databaseUsername] VARCHAR(255),
    [databasePassword] VARCHAR(255),
    [databaseName] VARCHAR(255),
    [databasePort] INTEGER,
    [createdOn] TIMESTAMP,
    [updatedOn] TIMESTAMP
);

-- SQLite does not support foreign keys; this is just for reference
-- FOREIGN KEY ([siteId]) REFERENCES site ([id])

-----------------------------------------------------------------------
-- site
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [site];

CREATE TABLE [site]
(
    [id] INTEGER NOT NULL PRIMARY KEY,
    [provider] VARCHAR(255),
    [uuid] VARCHAR(255),
    [realm] VARCHAR(255),
    [name] VARCHAR(255),
    [title] VARCHAR(255),
    [vcsUrl] VARCHAR(255),
    [vcsType] VARCHAR(255),
    [vcsProtocol] VARCHAR(255),
    [sshPort] INTEGER,
    [createdOn] TIMESTAMP,
    [updatedOn] TIMESTAMP
);
