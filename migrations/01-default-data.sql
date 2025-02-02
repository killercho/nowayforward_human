USE nwfh;

INSERT IGNORE INTO Users (Username, Password, Role)
VALUES
    -- Anon cannot be logged into
    ("Anon", "", "User"),
    -- Admin password is "Admin"
    ("Admin", "$2y$10$uAlsPtyuQm/xrKIKGTnnl.VSEKcq6hpKk2ndJGYF3x1YHvTLHY.ai", "Admin");
