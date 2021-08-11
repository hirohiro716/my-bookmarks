
/* Drop Tables */

DROP TABLE [bookmark];
DROP TABLE [setting];




/* Create Tables */

CREATE TABLE [bookmark]
(
	[url] text NOT NULL UNIQUE,
	[name] text NOT NULL,
	[icon_url] text,
	[labeling] text,
	[sort_number] integer NOT NULL,
	PRIMARY KEY ([url])
);


CREATE TABLE [setting]
(
	[name] text NOT NULL UNIQUE,
	[value] text NOT NULL,
	PRIMARY KEY ([name])
);



