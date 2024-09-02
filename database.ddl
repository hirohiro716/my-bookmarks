CREATE TABLE setting (
    name text NOT NULL UNIQUE,
    value text NOT NULL,
    PRIMARY KEY (name)
);

CREATE TABLE cookie_authentication (
    id text NOT NULL UNIQUE,
    access_time text NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE bookmark (
    id integer NOT NULL UNIQUE PRIMARY KEY AUTOINCREMENT,
    url text NOT NULL,
    name text NOT NULL,
    icon_url text,
    labeling text,
    sort_number integer NOT NULL
);

