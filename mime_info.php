<?php

// name, mime_type, pattern_offset, pattern_len, pattern, disposition

$MIME_INFO = array('ZIP'	=> array('application/zip',				'attachment',	0,	8,	'(PK00)?PK\x03\x04'),
				   'GIF'	=> array('image/gif',					'inline',		0,	6,	'GIF\d\d\w'),
				   'PNG'	=> array('image/png',					'inline',		0,	8,	'\x89PNG\r\n\x1A\n'),
				   'JPG'	=> array('image/jpeg',					'inline',		0,	10,	'\xFF\xD8[\x00-\xFF]{4}JFIF'),
				   'JPEG'	=> array('image/jpeg',					'inline',		0,	10,	'\xFF\xD8[\x00-\xFF]{4}JFIF'),
				   'BMP'	=> array('image/bmp',					'inline',		0,	2,	'BM'),
				   'TXT'	=> array('text/plain',					'inline',		0,	0,	''),
				   'HTM'	=> array('text/html',					'inline',		0,	0,	''),
				   'HTML'	=> array('text/html',					'inline',		0,	0,	''),
				   'GAM'	=> array('application/octet-stream',	'attachment',	0,	6,	'(GAME|GAPP)\x07\x02'),
				   'CCA'	=> array('application/octet-stream',	'attachment',	0,	5,	'CnC2U'),
				   'EXE'	=> array('application/x-sdlc',			'attachment',	0,	4,	'MZ(\x50|\x90)\x00'),
				   'RAR'	=> array('application/octet-stream',	'attachment',	0,	4,	'[Rr][Aa][Rr]!'),
);

?>