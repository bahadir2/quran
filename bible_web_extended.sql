CREATE TABLE bible (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    book VARCHAR(64),
    chapter INTEGER,
    verse INTEGER,
    text TEXT
);

INSERT INTO bible (book, chapter, verse, text) VALUES
('Genesis', 1, 1, 'In the beginning, God created the heavens and the earth.'),
('Genesis', 1, 2, 'The earth was formless and empty. Darkness was on the surface of the deep and God’s Spirit was hovering over the surface of the waters.'),
('Genesis', 1, 3, 'God said, “Let there be light,” and there was light.'),
('Exodus', 20, 1, 'God spoke all these words, saying,'),
('Exodus', 20, 2, 'I am Yahweh your God, who brought you out of the land of Egypt, out of the house of bondage.'),
('Exodus', 20, 3, 'You shall have no other gods before me.'),
('John', 1, 1, 'In the beginning was the Word, and the Word was with God, and the Word was God.'),
('John', 1, 2, 'He was in the beginning with God.'),
('John', 1, 3, 'All things were made through him. Without him, nothing was made that has been made.'),
('John', 3, 1, 'Now there was a man of the Pharisees named Nicodemus, a ruler of the Jews.'),
('John', 3, 2, 'He came to Jesus by night and said to him, “Rabbi, we know that you are a teacher come from God.”'),
('John', 3, 3, 'Jesus answered him, “Most certainly, I tell you, unless one is born anew, he can’t see God’s Kingdom.”'),
('John', 3, 4, 'For God so loved the world, that he gave his one and only Son, that whoever believes in him should not perish, but have eternal life.'),
('John', 3, 5, 'For God didn’t send his Son into the world to judge the world, but that the world should be saved through him.');