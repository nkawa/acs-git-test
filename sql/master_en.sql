----------------------------------------------------------------
-- akademikkukomyuniteishisutemu
-- $Id: master_en.sql,v 1.8 2007/03/28 05:58:09 w-ota Exp $
----------------------------------------------------------------


-- toranzakushonkaishi
BEGIN;


--------------------------------
-- masuta
--------------------------------

-- komyuniteishubetsumasuta
CREATE TABLE community_type_master_en (
	community_type_code CHAR(2) NOT NULL,  -- komyuniteishubetsuko^do (P)
	community_type_name TEXT NOT NULL,     -- komyuniteishubetsumei
	CONSTRAINT community_type_master_en_pkey1 PRIMARY KEY (community_type_code)
);

COPY community_type_master_en FROM stdin;
10	My Page
20	My Friends
30	My Friends Group
40	Community
\.

-- komyuniteimenbashubetsumasuta
CREATE TABLE community_member_type_master_en (
	community_member_type_code CHAR(2) NOT NULL,  -- komyuniteishubetsuko^do (P)
	community_member_type_name TEXT NOT NULL,     -- komyuniteishubetsumei
	CONSTRAINT community_mmb_type_mst_en_pk PRIMARY KEY (community_member_type_code)  -- 31mojiseigen
);

COPY community_member_type_master_en FROM stdin;
10	Administrator
20	Member
\.

-- kategoriguru^pumasuta
CREATE TABLE category_group_master_en (
	category_group_code CHAR(4) NOT NULL,  -- kategoriguru^puko^do (P)
	category_group_name TEXT NOT NULL,     -- kategoriguru^pumei
	CONSTRAINT category_group_master_en_pkey PRIMARY KEY (category_group_code)
);

-- kategorimasuta
CREATE TABLE category_master_en (
	category_code CHAR(4) NOT NULL,        -- kategoriko^do (P)
	category_name TEXT NOT NULL,           -- kategorimei
	category_group_code CHAR(4) NOT NULL,  -- kategoriguru^puko^do (F)
	CONSTRAINT category_master_en_pkey PRIMARY KEY (category_code),
	CONSTRAINT category_master_en_fkey1 FOREIGN KEY (category_group_code) REFERENCES category_group_master_en (category_group_code)
);

-- kontentsushubetsumasuta
CREATE TABLE contents_type_master_en (
	contents_type_code CHAR(2) NOT NULL,   -- kontentsushubetsuko^do (P)
	contents_type_name TEXT NOT NULL,      -- kontentsushubetsumei
	CONSTRAINT contents_type_master_en_pkey PRIMARY KEY (contents_type_code)
);

COPY contents_type_master_en FROM stdin;
00	Self
01	Name
02	Mail Address
03	Affiliation
04	Major
05	Place of Birth
06	Date of Birth
07	Profile
08	Login User Profile
09	Friend Profile
11	My Friends
21	Diary
31	Folder
32	Folder/Folder
33	File
41	Bulletin Board
42	Bulletin Board Thread
43	Private Community Bulletin Board Thread
51	mail_lang
52	Last Login
53	My Page Design
61	Community ML Address
62	Community ML Status
63	External RSS URL
64	External RSS Post User
65	External RSS ML Send Flag
66	External RSS Public Release Term
\.

-- CREATE INDEX contents_type_master_en_index1 ON contents_type_master_en (contents_type_name);

-- koukaireberumasuta
CREATE TABLE open_level_master_en (
	open_level_code CHAR(2) NOT NULL,                -- koukaireberuko^do (P)
	open_level_name TEXT NOT NULL,                   -- koukaireberumei
	open_for_public BOOLEAN NOT NULL,                -- ippanyu^zanikoukai
	open_for_user BOOLEAN NOT NULL,                  -- roguinyu^zanikoukai
	open_for_member BOOLEAN NOT NULL,                -- menbanikoukai
	open_for_administrator BOOLEAN NOT NULL,         -- kanrisha(honninyakomyuniteikanrisha)nikoukai
	open_for_system_administrator BOOLEAN NOT NULL,  -- shisutemukanrishanikoukai
	CONSTRAINT open_level_master_en_pkey PRIMARY KEY (open_level_code)
);

COPY open_level_master_en FROM stdin;
01	Open to General Public	t	t	t	t	t
02	Open to Logged in Users	f	t	t	t	t
03	Private	f	f	f	t	t
04	Private (Members Only)	f	f	t	t	t
05	Open to Friends	f	f	t	t	t
06	Public Release	t	t	t	t	t
\.

-- taikishubetsumasuta
CREATE TABLE waiting_type_master_en (
	waiting_type_code CHAR(2) NOT NULL,  -- taikishubetsuko^do (P)
	waiting_type_name TEXT NOT NULL,     -- taikishubetsumei
	CONSTRAINT waiting_type_master_en_pkey PRIMARY KEY (waiting_type_code)
);

COPY waiting_type_master_en FROM stdin;
10	Add to My Friends
20	Add to Community
30	Invite to Community
40	Add Parent Community
50	Add Sub Community
\.

-- taikijoutaimasuta
CREATE TABLE waiting_status_master_en (
	waiting_status_code CHAR(2) NOT NULL,  -- taikijoutaiko^do (P)
	waiting_status_name TEXT NOT NULL,     -- taikijoutaimei
	CONSTRAINT waiting_status_master_en_pkey PRIMARY KEY (waiting_status_code)
);

COPY waiting_status_master_en FROM stdin;
10	Approval Pending
20	Approved
30	Rejected
\.

-- fairushuruimasuta
CREATE TABLE file_category_master_en (
	file_category_code CHAR(4) NOT NULL,  -- fairushuruiko^do (P)
	file_category_name TEXT NOT NULL,     -- fairushuruimei
	CONSTRAINT file_category_master_en_pkey PRIMARY KEY (file_category_code)
);

COPY file_category_master_en FROM stdin;
0000	Not Specified
0001	Article
0002	Presentation
0003	Picture
0004	Movie
\.

-- fairukontentsushubetsumasuta
CREATE TABLE file_contents_type_master_en (
	file_contents_type_code CHAR(4) NOT NULL,  -- fairukontentsushubetsuko^do (P)
	file_contents_type_name TEXT NOT NULL,     -- fairukontentsushubetsumei
	CONSTRAINT file_contents_type_mst_en_pkey PRIMARY KEY (file_contents_type_code)
);

COPY file_contents_type_master_en FROM stdin;
0001	Author
0002	Title
0003	Journal/Association
0004	Volume Number
0005	Page (from)
0006	Page (to)
0007	Date of Announcement/Publication
0008	Language
0009	Venue
0010	Publisher
0011	Article Type
0012	Remarks
\.

-- fairurirekisousamasuta
CREATE TABLE file_history_operation_master_en (
	file_history_operation_code CHAR(4) NOT NULL,  -- fairurirekisousako^do (P)
	file_history_operation_name TEXT NOT NULL,  -- fairurirekisousamei
	CONSTRAINT file_history_ope_mst_en_pkey PRIMARY KEY (file_history_operation_code)  -- 31mojiseigen: operation (kigou) ope totanshuku
);

COPY file_history_operation_master_en FROM stdin;
0101	New
0201	Refresh
0301	Revive
\.

-- shisutemusetteiguru^pu
CREATE TABLE system_config_group_en (
	system_config_group_code CHAR(2) NOT NULL,  -- guru^puko^do (P)
	system_config_group_name TEXT NOT NULL,     -- guru^pumei
	display_order INTEGER NOT NULL,             -- hyoujijunjo
	CONSTRAINT system_config_group_en_pkey PRIMARY KEY (system_config_group_code)
);

COPY system_config_group_en FROM stdin;
01	System	1
02	My Page	2
03	Community	3
04	Graphics File	4
05	Ranking	5
06	Public Release	6
07	Log	7
\.



-- kategoriguru^pumasuta
COPY category_group_master_en FROM stdin;
0001	Research
0002	Education
0003	Field
0004	Occupation
0005	Club/Circle
0006	Interest/Pastime
0007	Other
\.

-- kategorimasuta
COPY category_master_en FROM stdin;
0001	Laboratory	0001
0002	Research Group	0001
0003	Study Group	0001
0004	Collaborative Research	0001
0005	Project	0001
0006	Seminar	0001
0007	Academic Association	0001
0008	Symposium	0001
0009	Research Association	0001
0010	Workshop	0001
0011	Lecture	0002
0012	Special Lecture	0002
0013	Lifelong Education	0002
0014	Hardware	0003
0015	Software	0003
0016	Network	0003
0017	Database	0003
0018	Algorithm	0003
0019	Speech Processing	0003
0020	Graphical/Image Processing	0003
0021	Media Processing	0003
0022	Pattern Information Processing	0003
0023	Fundamental Mathematics	0003
0024	Mathematical Model	0003
0025	Computation Theory	0003
0026	Integrated System	0003
0027	Logical Circuit	0003
0028	Installed System	0003
0029	Operating System	0003
0030	System Control	0003
0031	Programming	0003
0032	Natural Language Processing	0003
0033	Security	0003
0034	Knowledge Information Processin	0003
0035	Biological Information Processi	0003
0036	Mobile	0003
0037	Ubiquitous	0003
0038	Healthcare	0003
0039	Welfare	0003
0040	Communication	0003
0041	Grid	0003
0042	Cognitive Science	0003
0043	Media Application	0003
0044	Simulation	0003
0045	multiple degree of freedom syst	0003
0046	Material Information	0003
0047	Life Information	0003
0048	Large Dimension Computing	0003
0049	General Medical	0003
0050	General Science	0003
0051	General Biology	0003
0052	General Physics	0003
0053	Information Model	0003
0054	Decentralized Coordination	0003
0055	Intellectual Assistance	0003
0056	Information Design	0003
0057	Philosophy/Ethics	0003
0058	General Education	0003
0059	Language	0003
0060	Education	0003
0061	Psychology	0003
0062	Economy	0003
0063	Literature	0003
0064	Law	0003
0065	Society	0003
0066	Health and Healthcare	0003
0067	Undergraduate School	0004
0068	School	0004
0069	Graduate School	0004
0070	Major	0004
0071	Course	0004
0072	Committee	0004
0073	Humanities	0005
0074	Sports	0005
0075	Book/Author	0006
0076	Music/Artist	0006
0077	Movie/TV/Animation/Movie Star	0006
0078	Game	0006
0079	Car/Bike	0006
0080	Travel	0006
0081	Internet/Computer	0006
0082	Food/Restaurant	0006
0083	Fashion	0006
0084	Pet/Animal	0006
0085	Sports/Outdoors	0006
0086	Qualifications/Certificates	0006
0087	General Interest/Pastime	0006
0088	Other	0007
\.


----------------------------------------------------
-- 関数: TIMESTAMPをYYYY/MM/DD(wday) H:MMに変換する
--     $1:
--         YYYY/MM/DD => 2005/01/02
--         FMMM/FMDD  => 1/2
--     $2:
--         FMHH24:MI:SS   => 1:02:03
--         FMHH24:MI      => 1:02
----------------------------------------------------
CREATE FUNCTION acs_convert_timestamp_en(TIMESTAMP, TEXT, TEXT)
RETURNS TEXT AS '
  SELECT
    to_char($1, $2 || ''('' || jday || '') '' || $3)::TEXT
  FROM
    (SELECT
       CASE
         WHEN wday = ''1'' THEN ''Sun''
         WHEN wday = ''2'' THEN ''Mon''
         WHEN wday = ''3'' THEN ''Tue''
         WHEN wday = ''4'' THEN ''Wed''
         WHEN wday = ''5'' THEN ''Thu''
         WHEN wday = ''6'' THEN ''Fri''
         WHEN wday = ''7'' THEN ''Sat''
       END AS jday
     FROM
       (SELECT to_char($1, ''D'') AS wday) AS dummy1
    ) AS dummy2
' LANGUAGE 'sql';


COMMIT;


----------------------------------------------------------------


-- EOF
