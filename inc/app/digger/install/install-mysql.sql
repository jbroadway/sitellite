CREATE TABLE digger_linkstory (
	id INT AUTO_INCREMENT PRIMARY KEY,
	link CHAR(128) NOT NULL,
	user CHAR(48) NOT NULL,
	posted_on DATETIME NOT NULL,
	score INT NOT NULL,
	title CHAR(128) NOT NULL,
	category INT NOT NULL,
	description TEXT NOT NULL,
	status ENUM('enabled','disabled') NOT NULL,
	INDEX (user, posted_on, category, status, score)
);

CREATE TABLE digger_category (
	id INT AUTO_INCREMENT PRIMARY KEY,
	category CHAR(128) NOT NULL,
	INDEX (category)
);

CREATE TABLE digger_comments (
	id INT AUTO_INCREMENT PRIMARY KEY,
	story INT NOT NULL, 
	user CHAR(48) NOT NULL,
	comment_date DATETIME NOT NULL,
	comments TEXT NOT NULL,
	INDEX (story, user, comment_date)
);

CREATE TABLE digger_vote (
	id INT AUTO_INCREMENT PRIMARY KEY,
	story INT NOT NULL,
	score TINYINT NOT NULL,
	user CHAR(48) NOT NULL,
	ip CHAR(15) NOT NULL,
	votetime DATETIME NOT NULL,
	INDEX (story, user)
);
