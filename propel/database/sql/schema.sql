
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
    [updated] TIMESTAMP
);
