
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
