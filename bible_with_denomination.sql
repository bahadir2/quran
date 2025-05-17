
-- Create the bible table
CREATE TABLE bible (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    book TEXT NOT NULL,
    chapter INTEGER NOT NULL,
    verse INTEGER NOT NULL,
    text TEXT NOT NULL,
    denomination TEXT NOT NULL
);

-- Insert sample data
INSERT INTO bible (book, chapter, verse, text, denomination) VALUES
('Genesis', 1, 1, 'In the beginning God created the heaven and the earth.', 'Protestant,Catholic'),
('Tobit', 1, 1, 'This is the book of the acts of Tobit...', 'Catholic,Orthodox'),
('Matthew', 5, 9, 'Blessed are the peacemakers: for they shall be called the children of God.', 'Protestant,Catholic,Orthodox'),
('Wisdom', 1, 1, 'Love justice, you who judge the earth.', 'Catholic,Orthodox'),
('John', 3, 16, 'For God so loved the world, that he gave his only begotten Son...', 'Protestant,Catholic,Orthodox');
