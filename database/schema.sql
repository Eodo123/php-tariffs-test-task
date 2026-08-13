CREATE TABLE tariffs (
                         id INTEGER PRIMARY KEY AUTOINCREMENT,
                         name TEXT NOT NULL UNIQUE,
                         description TEXT,
                         speed INTEGER NOT NULL,
                         price REAL NOT NULL,
                         created_at DATETIME NOT NULL,
                         expires_at DATETIME
);
