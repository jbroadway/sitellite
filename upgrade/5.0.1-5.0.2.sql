alter table sitellite_user change column teams teams text not null default '';
INSERT INTO xed_attributes VALUES (null, 'default', 'style', "type=text\nalt=Style");
