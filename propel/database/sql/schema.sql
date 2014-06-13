
-----------------------------------------------------------------------
-- environment
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [environment];

CREATE TABLE [environment]
(
    [id] INTEGER NOT NULL PRIMARY KEY,
    [site_id] INTEGER,
    [name] VARCHAR(255) NOT NULL,
    [host] VARCHAR(255),
    [username] VARCHAR(255),
    [branch] VARCHAR(255),
    [createdOn] TIMESTAMP,
    [updatedOn] TIMESTAMP
);
