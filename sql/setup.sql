----------------------------------------------------------------
-- �����ǥߥå����ߥ�˥ƥ������ƥ�
-- $Id: setup.sql,v 1.77 2007/03/30 05:08:15 w-ota Exp $
----------------------------------------------------------------


-- �ȥ�󥶥�����󳫻�
BEGIN;


--------------------------------
-- �ޥ���
--------------------------------

-- ���ߥ�˥ƥ����̥ޥ���
CREATE TABLE community_type_master (
	community_type_code CHAR(2) NOT NULL,  -- ���ߥ�˥ƥ����̥����� (P)
	community_type_name TEXT NOT NULL,     -- ���ߥ�˥ƥ�����̾
	CONSTRAINT community_type_master_pkey1 PRIMARY KEY (community_type_code)
);

COPY community_type_master FROM stdin;
10	�ޥ��ڡ���
20	�ޥ��ե��
30	�ޥ��ե�󥺥��롼��
40	���ߥ�˥ƥ�
\.

-- ���ߥ�˥ƥ����м��̥ޥ���
CREATE TABLE community_member_type_master (
	community_member_type_code CHAR(2) NOT NULL,  -- ���ߥ�˥ƥ����̥����� (P)
	community_member_type_name TEXT NOT NULL,     -- ���ߥ�˥ƥ�����̾
	CONSTRAINT community_member_type_master_pk PRIMARY KEY (community_member_type_code)  -- 31ʸ������
);

COPY community_member_type_master FROM stdin;
10	������
20	����
\.

-- ���ƥ��ꥰ�롼�ץޥ���
CREATE TABLE category_group_master (
	category_group_code CHAR(4) NOT NULL,  -- ���ƥ��ꥰ�롼�ץ����� (P)
	category_group_name TEXT NOT NULL,     -- ���ƥ��ꥰ�롼��̾
	CONSTRAINT category_group_master_pkey PRIMARY KEY (category_group_code)
);

-- ���ƥ���ޥ���
CREATE TABLE category_master (
	category_code CHAR(4) NOT NULL,        -- ���ƥ��ꥳ���� (P)
	category_name TEXT NOT NULL,           -- ���ƥ���̾
	category_group_code CHAR(4) NOT NULL,  -- ���ƥ��ꥰ�롼�ץ����� (F)
	CONSTRAINT category_master_pkey PRIMARY KEY (category_code),
	CONSTRAINT category_master_fkey1 FOREIGN KEY (category_group_code) REFERENCES category_group_master (category_group_code)
);

-- ����ƥ�ļ��̥ޥ���
CREATE TABLE contents_type_master (
	contents_type_code CHAR(2) NOT NULL,   -- ����ƥ�ļ��̥����� (P)
	contents_type_name TEXT NOT NULL,      -- ����ƥ�ļ���̾
	CONSTRAINT contents_type_master_pkey PRIMARY KEY (contents_type_code)
);

COPY contents_type_master FROM stdin;
00	����
01	��̾
02	�᡼�륢�ɥ쥹
03	��°
04	�칶
05	�п�
06	��ǯ����
07	�ץ�ե�����
08	�ץ�ե�����_������桼��
09	�ץ�ե�����_�ե���
11	�ޥ��ե��
21	�������꡼
31	�ե����
32	�ե����.�ե����
33	�ե�����
41	�ŻҷǼ���
42	�ŻҷǼ���.����å�
43	�ŻҷǼ���.����å�_��������ߥ�˥ƥ�
51	�᡼�����
52	�饹�ȥ�����
53	�ޥ��ڡ����ǥ�����
61	���ߥ�˥ƥ�ML���ɥ쥹
62	���ߥ�˥ƥ�ML���ơ�����
63	����RSS.URL
64	����RSS.��Ƽ�
65	����RSS.ML����
66	����RSS.�ѥ֥�å���꡼������
\.

-- CREATE INDEX contents_type_master_index1 ON contents_type_master (contents_type_name);

-- ������٥�ޥ���
CREATE TABLE open_level_master (
	open_level_code CHAR(2) NOT NULL,                -- ������٥륳���� (P)
	open_level_name TEXT NOT NULL,                   -- ������٥�̾
	open_for_public BOOLEAN NOT NULL,                -- ���̥桼���˸���
	open_for_user BOOLEAN NOT NULL,                  -- ������桼���˸���
	open_for_member BOOLEAN NOT NULL,                -- ���Ф˸���
	open_for_administrator BOOLEAN NOT NULL,         -- ������(�ܿͤ䥳�ߥ�˥ƥ�������)�˸���
	open_for_system_administrator BOOLEAN NOT NULL,  -- �����ƥ�����Ԥ˸���
	CONSTRAINT open_level_master_pkey PRIMARY KEY (open_level_code)
);

COPY open_level_master FROM stdin;
01	���̸���	t	t	t	t	t
02	������桼���˸���	f	t	t	t	t
03	�����	f	f	f	t	t
04	����� (���ФΤ�)	f	f	t	t	t
05	ͧ�ͤ˸���	f	f	t	t	t
06	�ѥ֥�å���꡼��	t	t	t	t	t
\.

-- ������٥�ꥹ�� (���ߥ�˥ƥ����̤��ݻ����������٥�Υꥹ��)
CREATE TABLE open_level_list (
	community_type_code CHAR(2) NOT NULL,     -- ���ߥ�˥ƥ����̥����� (P) (F)
	contents_type_code CHAR(2) NOT NULL,      -- ����ƥ�ļ��̥����� (P) (F)
	open_level_code CHAR(2) NOT NULL,         -- ������٥륳���� (P) (F)
	display_order INTEGER NOT NULL,           -- ɽ�����
	is_default BOOLEAN NOT NULL DEFAULT 'f',  -- �ǥե�����ͤ��ɤ���
	CONSTRAINT open_level_list_pkey PRIMARY KEY (community_type_code, contents_type_code, open_level_code),
	CONSTRAINT open_level_list_fkey1 FOREIGN KEY (community_type_code) REFERENCES community_type_master (community_type_code),
	CONSTRAINT open_level_list_fkey2 FOREIGN KEY (contents_type_code) REFERENCES contents_type_master (contents_type_code),
	CONSTRAINT open_level_list_fkey3 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);

COPY open_level_list FROM stdin;
10	00	01	1	f
10	00	03	2	t
10	01	01	1	t
10	01	02	2	f
10	01	03	3	f
10	02	03	0	t
10	03	01	0	t
10	04	01	0	t
10	05	01	1	f
10	05	02	2	t
10	05	03	4	f
10	05	05	3	f
10	06	01	1	f
10	06	02	2	t
10	06	03	4	f
10	06	05	3	f
10	07	01	0	t
10	08	02	0	t
10	09	05	0	t
10	11	01	1	t
10	11	02	2	f
10	21	01	1	f
10	21	02	2	t
10	21	05	3	f
10	21	03	4	f
10	31	01	1	f
10	31	02	2	t
10	31	05	3	f
10	31	03	4	f
10	32	01	1	f
10	32	02	2	t
10	32	05	3	f
10	32	03	4	f
10	51	01	1	t
10	52	02	0	t
10	53	03	0	t
40	00	01	1	t
40	00	03	2	f
40	07	01	0	t
40	31	01	1	f
40	31	02	2	t
40	31	04	3	f
40	32	01	1	f
40	32	02	2	t
40	32	04	3	f
40	41	01	1	f
40	41	02	2	t
40	41	04	3	f
40	42	01	1	f
40	42	02	2	t
40	42	04	3	f
40	42	06	4	f
40	43	04	3	f
40	61	04	1	t
40	62	04	1	t
40	63	01	1	f
40	63	02	2	t
40	63	04	3	f
40	63	06	4	f
40	64	04	1	t
40	65	04	1	t
40	66	04	1	t
\.

-- �Ե����̥ޥ���
CREATE TABLE waiting_type_master (
	waiting_type_code CHAR(2) NOT NULL,  -- �Ե����̥����� (P)
	waiting_type_name TEXT NOT NULL,     -- �Ե�����̾
	CONSTRAINT waiting_type_master_pkey PRIMARY KEY (waiting_type_code)
);

COPY waiting_type_master FROM stdin;
10	�ޥ��ե���ɲ�
20	���ߥ�˥ƥ�����
30	���ߥ�˥ƥ�����
40	�ƥ��ߥ�˥ƥ��ɲ�
50	���֥��ߥ�˥ƥ��ɲ�
\.

-- �Ե����֥ޥ���
CREATE TABLE waiting_status_master (
	waiting_status_code CHAR(2) NOT NULL,  -- �Ե����֥����� (P)
	waiting_status_name TEXT NOT NULL,     -- �Ե�����̾
	CONSTRAINT waiting_status_master_pkey PRIMARY KEY (waiting_status_code)
);

COPY waiting_status_master FROM stdin;
10	��ǧ�Ԥ�
20	��ǧ�Ѥ�
30	��ǧ����
\.

-- �ե��������ޥ���
CREATE TABLE file_category_master (
	file_category_code CHAR(4) NOT NULL,  -- �ե�������ॳ���� (P)
	file_category_name TEXT NOT NULL,     -- �ե��������̾
	CONSTRAINT file_category_master_pkey PRIMARY KEY (file_category_code)
);

COPY file_category_master FROM stdin;
0000	����ʤ�
0001	��ʸ
0002	�ץ쥼��
0003	����
0004	ư��
\.

-- �ե����륳��ƥ�ļ��̥ޥ���
CREATE TABLE file_contents_type_master (
	file_contents_type_code CHAR(4) NOT NULL,  -- �ե����륳��ƥ�ļ��̥����� (P)
	file_contents_type_name TEXT NOT NULL,     -- �ե����륳��ƥ�ļ���̾
	CONSTRAINT file_contents_type_master_pkey PRIMARY KEY (file_contents_type_code)
);

COPY file_contents_type_master FROM stdin;
0001	����
0002	ɽ��
0003	�Ǻܻ�ز�
0004	VolNo.��
0005	�ڡ���from
0006	�ڡ���to
0007	ȯɽ���Ǻ���
0008	����
0009	������
0010	���Ǽ�
0011	��ʸ����
0012	����
\.

-- �ե����륳��ƥ�ļ��̥ꥹ��
CREATE TABLE file_contents_type_list (
	file_category_code CHAR(4) NOT NULL,       -- �ե�������ॳ���� (P) (F)
	file_contents_type_code CHAR(4) NOT NULL,  -- �ե����륳��ƥ�ļ��̥����� (P) (F)
	CONSTRAINT file_contents_type_list_pkey PRIMARY KEY (file_category_code, file_contents_type_code),
	CONSTRAINT file_contents_type_list_fkey1 FOREIGN KEY (file_category_code) REFERENCES file_category_master (file_category_code),
	CONSTRAINT file_contents_type_list_fkey2 FOREIGN KEY (file_contents_type_code) REFERENCES file_contents_type_master (file_contents_type_code)
);

COPY file_contents_type_list FROM stdin;
0000	0002
0000	0012
0001	0001
0001	0002
0001	0003
0001	0004
0001	0005
0001	0006
0001	0007
0001	0008
0001	0009
0001	0010
0001	0011
0001	0012
0002	0001
0002	0002
0002	0003
0002	0007
0002	0008
0002	0009
0002	0012
0003	0001
0003	0002
0003	0012
0004	0001
0004	0002
0004	0012
\.

-- �ե������������ޥ���
CREATE TABLE file_history_operation_master (
	file_history_operation_code CHAR(4) NOT NULL,  -- �ե��������������� (P)
	file_history_operation_name TEXT NOT NULL,  -- �ե������������̾
	CONSTRAINT file_history_ope_master_pkey PRIMARY KEY (file_history_operation_code)  -- 31ʸ������: operation �� ope ��û��
);

COPY file_history_operation_master FROM stdin;
0101	����
0201	����
0301	����
\.


--------------------------------
-- ���ߥ�˥ƥ�
--------------------------------

-- ���ߥ�˥ƥ�
CREATE TABLE community (
	community_id BIGINT NOT NULL,                          -- ���ߥ�˥ƥ�ID (P)
	community_name TEXT,                                   -- ���ߥ�˥ƥ�̾
	community_type_code CHAR(2) NOT NULL,                  -- ���ߥ�˥ƥ����̥����� (F)
	category_code CHAR(4),                                 -- ���ƥ��ꥳ���� (F)
	admission_flag BOOLEAN NOT NULL DEFAULT 'f',           -- ��ͳ���åե饰
	register_date TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP,  -- ���ߥ�˥ƥ���Ͽ����
	delete_flag BOOLEAN NOT NULL DEFAULT 'f',              -- ����ե饰
	CONSTRAINT community_pkey PRIMARY KEY (community_id),
	CONSTRAINT community_fkey1 FOREIGN KEY (community_type_code) REFERENCES community_type_master (community_type_code),
	CONSTRAINT community_fkey2 FOREIGN KEY (category_code) REFERENCES category_master (category_code)

);
CREATE SEQUENCE community_id_seq;

-- ���֥��ߥ�˥ƥ�
CREATE TABLE sub_community (
	community_id BIGINT NOT NULL,      -- �ƥ��ߥ�˥ƥ�ID (P) (F)
	sub_community_id BIGINT NOT NULL,  -- ���֥��ߥ�˥ƥ�ID (P) (F)
	CONSTRAINT sub_community_pkey PRIMARY KEY (community_id, sub_community_id),
	CONSTRAINT sub_community_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT sub_community_fkey2 FOREIGN KEY (sub_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- ���ߥ�˥ƥ�����
CREATE TABLE community_member (
	community_id BIGINT NOT NULL,        -- ���ߥ�˥ƥ�ID (P) (F)
	user_community_id BIGINT NOT NULL,   -- ���ФȤʤ륳�ߥ�˥ƥ�ID(�ޥ��ڡ���) (P) (F)
	community_member_type_code CHAR(2),  -- ���ߥ�˥ƥ����м��̥�����(NULL����) (P)
	CONSTRAINT community_member_pkey PRIMARY KEY (community_id, user_community_id),
	CONSTRAINT community_member_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT community_member_fkey2 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT community_member_fkey3 FOREIGN KEY (community_member_type_code) REFERENCES community_member_type_master (community_member_type_code)
);

-- (���ߥ�˥ƥ���)����ƥ��
CREATE TABLE contents (
	community_id BIGINT NOT NULL,         -- ���ߥ�˥ƥ�ID (P) (F)
	contents_type_code CHAR(2) NOT NULL,  -- ����ƥ�ļ��̥����� (P) (F)
	contents_value TEXT,                  -- ����ƥ����
	open_level_code CHAR(2) NOT NULL,     -- ������٥륳���� (P) (F)
	CONSTRAINT contents_pkey PRIMARY KEY (community_id, contents_type_code, open_level_code),
	CONSTRAINT contents_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT contents_fkey2 FOREIGN KEY (contents_type_code) REFERENCES contents_type_master (contents_type_code),
	CONSTRAINT contents_fkey3 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);

-- CREATE INDEX contents_index1 ON contents (community_id);
-- CREATE INDEX contents_index2 ON contents (contents_type_code);
-- CREATE INDEX contents_index3 ON contents (open_level_code);

-- ����ƥ�� ����Ѥߥ��ߥ�˥ƥ�
CREATE TABLE contents_trusted_community (
	community_id BIGINT NOT NULL,          -- ���ߥ�˥ƥ�ID (P) (F)
	contents_type_code CHAR(2) NOT NULL,   -- ����ƥ�ļ��̥����� (P) (F)
	open_level_code CHAR(2) NOT NULL,     -- ������٥륳���� (P) (F)
	trusted_community_id BIGINT NOT NULL,  -- ����Ѥߥ��ߥ�˥ƥ�ID (P) (F)
	CONSTRAINT contents_trusted_community_pkey PRIMARY KEY (community_id, contents_type_code, open_level_code, trusted_community_id),
	CONSTRAINT contents_trusted_community_fk1 FOREIGN KEY (community_id, contents_type_code, open_level_code) REFERENCES contents (community_id, contents_type_code, open_level_code) ON DELETE CASCADE,  -- 31ʸ������
	CONSTRAINT contents_trusted_community_fk2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE  -- 31ʸ������
);

-- ���� ����Ѥߥ��ߥ�˥ƥ�
CREATE TABLE join_trusted_community (
	community_id BIGINT NOT NULL,          -- ���ߥ�˥ƥ�ID (P) (F)
	trusted_community_id BIGINT NOT NULL,  -- ����Ѥߥ��ߥ�˥ƥ�ID (P) (F)
	CONSTRAINT join_trusted_community_pkey PRIMARY KEY (community_id, trusted_community_id),
	CONSTRAINT join_trusted_community_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT join_trusted_community_fkey2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- �Ե� (��ǧ�Ե�, �����Ե���)
CREATE TABLE waiting (
	waiting_id BIGINT NOT NULL,                                     -- �Ե�ID (P)
	community_id BIGINT NOT NULL,                                   -- ���ߥ�˥ƥ�ID (F)
	waiting_community_id BIGINT NOT NULL,                           -- �Ե����ߥ�˥ƥ�ID
	waiting_type_code CHAR(2) NOT NULL,                             -- �Ե����̥����� (F)
	waiting_status_code CHAR(2) NOT NULL,                           -- �Ե����֥����� (F)
	message TEXT,                                                   -- ��å�����
	reply_message TEXT,                                             -- �ֿ���å�����
	entry_user_community_id BIGINT NOT NULL,                        -- ��Ͽ�桼�����ߥ�˥ƥ�ID
	entry_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,     -- ��Ͽ��
	complete_user_community_id BIGINT,                              -- ��λ�桼�����ߥ�˥ƥ�ID
	complete_date TIMESTAMP(0),                                     -- ��λ��
	CONSTRAINT waiting_community_member_pkey PRIMARY KEY (waiting_id),
	CONSTRAINT waiting_community_member_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT waiting_community_member_fkey2 FOREIGN KEY (waiting_type_code) REFERENCES waiting_type_master (waiting_type_code),
	CONSTRAINT waiting_community_member_fkey3 FOREIGN KEY (waiting_status_code) REFERENCES waiting_status_master (waiting_status_code)
);

CREATE SEQUENCE waiting_id_seq;


--------------------------------
-- �������꡼
--------------------------------

-- �������꡼
CREATE TABLE diary (
	diary_id BIGINT NOT NULL,                                   -- �������꡼ID (P)
	community_id BIGINT NOT NULL,                               -- (�桼��)���ߥ�˥ƥ�ID (F)
	subject TEXT,                                               -- ��̾
	body TEXT NOT NULL,                                         -- ����
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- �������
	open_level_code CHAR(2) NOT NULL,                           -- �����ϰ� (F)
	diary_delete_flag BOOLEAN NOT NULL DEFAULT 'f',             -- ����ե饰
	CONSTRAINT diary_pkey PRIMARY KEY (diary_id),
	CONSTRAINT diary_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT diary_fkey2 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);
CREATE SEQUENCE diary_id_seq;

-- �������꡼������
CREATE TABLE diary_comment (
	diary_comment_id BIGINT NOT NULL,                           -- �������꡼������ID (P)
	diary_id BIGINT NOT NULL,                                   -- �������꡼ID(�Ƶ���) (F)
	user_community_id BIGINT NOT NULL,                          -- ��ƼԤΥ��ߥ�˥ƥ�ID(�ޥ��ڡ���)
	body TEXT NOT NULL,                                         -- ����
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- �������
	diary_comment_delete_flag BOOLEAN NOT NULL DEFAULT 'f',     -- ����ե饰
	CONSTRAINT diary_comment_pkey PRIMARY KEY (diary_comment_id),
	CONSTRAINT diary_comment_fkey1 FOREIGN KEY (diary_id) REFERENCES diary (diary_id) ON DELETE CASCADE
);
CREATE SEQUENCE diary_comment_id_seq;

-- �������꡼ ����Ѥߥ��ߥ�˥ƥ�
CREATE TABLE diary_trusted_community (
	diary_id BIGINT NOT NULL,              -- �������꡼ID (P) (F)
	trusted_community_id BIGINT NOT NULL,  -- ����Ѥߥ��ߥ�˥ƥ�ID (P) (F)
	CONSTRAINT diary_trusted_community_pkey PRIMARY KEY (diary_id, trusted_community_id),
	CONSTRAINT diary_trusted_community_fkey1 FOREIGN KEY (diary_id) REFERENCES diary (diary_id) ON DELETE CASCADE,
	CONSTRAINT diary_trusted_community_fkey2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- �������꡼������������
CREATE TABLE diary_access_history (
	user_community_id BIGINT NOT NULL,  -- ���������ԤΥ桼�����ߥ�˥ƥ�ID
	diary_id BIGINT NOT NULL,           -- ���������оݤΥ������꡼ID
	access_date TIMESTAMP(0) NOT NULL,  -- ������������
	CONSTRAINT diary_access_history_pkey PRIMARY KEY (user_community_id, diary_id),
	CONSTRAINT diary_access_history_fkey1 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT diary_access_history_fkey2 FOREIGN KEY (diary_id) REFERENCES diary (diary_id) ON DELETE CASCADE
);


--------------------------------
-- �ե����
--------------------------------

-- �ե��������
CREATE TABLE file_info (
	file_id BIGINT NOT NULL,                                      -- �ե�����ID (P)
	owner_community_id BIGINT NOT NULL,                           -- ��ͭ�ԥ��ߥ�˥ƥ�ID
	display_file_name TEXT NOT NULL,                              -- ɽ���ե�����̾
	server_file_name TEXT NOT NULL,                               -- �����Хե�����̾
	thumbnail_server_file_name TEXT,                              -- ����ͥ��륵���Хե�����̾
	rss_server_file_name TEXT,                                    -- RSS�����Хե�����̾
	mime_type TEXT,                                               -- MimeType
	file_size INTEGER,                                            -- �ե����륵����
	comment TEXT,                                                 -- ������
	entry_user_community_id BIGINT,                               -- ��Ͽ�桼�����ߥ�˥ƥ�ID
	entry_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- ��Ͽ��
	update_user_community_id BIGINT,                              -- �����桼�����ߥ�˥ƥ�ID
	update_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- ������
	CONSTRAINT file_info_pkey PRIMARY KEY (file_id),
	CONSTRAINT file_info_fkey1 FOREIGN KEY (owner_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);
CREATE SEQUENCE file_id_seq;

-- �ե����
CREATE TABLE folder (
	folder_id BIGINT NOT NULL,                                    -- �ե����ID (P)
	community_id BIGINT NOT NULL,                                 -- ���ߥ�˥ƥ�ID (F)
	folder_name TEXT NOT NULL,                                    -- �ե����̾
	comment TEXT,                                                 -- ������
	parent_folder_id BIGINT,                                      -- �ƥե����ID (F)
	entry_user_community_id BIGINT,                               -- ��Ͽ�桼�����ߥ�˥ƥ�ID
	entry_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- ��Ͽ��
	update_user_community_id BIGINT,                              -- �����桼�����ߥ�˥ƥ�ID
	update_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- ������
	open_level_code CHAR(2),                                      -- ������٥륳���� (F)
	CONSTRAINT folder_pkey PRIMARY KEY (folder_id),
	CONSTRAINT folder_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT folder_fkey2 FOREIGN KEY (parent_folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE,
	CONSTRAINT folder_fkey3 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);
CREATE SEQUENCE folder_id_seq;

-- �ե�����ե�����
CREATE TABLE folder_file (
	folder_id BIGINT NOT NULL,            -- �ե����ID (P) (F)
	file_id BIGINT NOT NULL,              -- �ե�����ID (P) (F)
	CONSTRAINT folder_file_pkey PRIMARY KEY (folder_id, file_id),
	CONSTRAINT folder_file_fkey1 FOREIGN KEY (folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE,
	CONSTRAINT folder_file_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);

-- �ե���� ����Ѥߥ��ߥ�˥ƥ�
CREATE TABLE folder_trusted_community (
	folder_id BIGINT NOT NULL,             -- �ե����ID (P) (F)
	trusted_community_id BIGINT NOT NULL,  -- ����Ѥߥ��ߥ�˥ƥ�ID (P) (F)
	PRIMARY KEY (folder_id, trusted_community_id),
	CONSTRAINT folder_trusted_community_fkey1 FOREIGN KEY (folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE,
	CONSTRAINT folder_trusted_community_fkey2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- �ץåȥ��ߥ�˥ƥ�
CREATE TABLE put_community (
	folder_id BIGINT NOT NULL,                -- �ե����ID (P) (F)
	put_community_id BIGINT NOT NULL,         -- �ץå��襳�ߥ�˥ƥ�ID (P) (F)
	put_community_folder_id BIGINT NOT NULL,  -- �ץå��襳�ߥ�˥ƥ��ե����ID (P) (F)
	CONSTRAINT put_community_pkey PRIMARY KEY (folder_id, put_community_id, put_community_folder_id),
	CONSTRAINT put_community_fkey1 FOREIGN KEY (folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE,
	CONSTRAINT put_community_fkey2 FOREIGN KEY (put_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT put_community_fkey3 FOREIGN KEY (put_community_folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE
);

-- �ե�����ܺپ���
CREATE TABLE file_detail_info (
	file_id BIGINT NOT NULL,                                      -- �ե�����ID (P) (F)
	file_category_code CHAR(4) NOT NULL,                          -- �ե����륫�ƥ��ꥳ���� (F)
	update_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- ������
	CONSTRAINT file_detail_info_pkey PRIMARY KEY (file_id),
	CONSTRAINT file_detail_info_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE,
	CONSTRAINT file_detail_info_fkey3 FOREIGN KEY (file_category_code) REFERENCES file_category_master (file_category_code)
);

-- �ե����륳��ƥ��(�ܺپ���)
CREATE TABLE file_contents (
	file_id BIGINT NOT NULL,                   -- �ե�����ID (P) (F)
	file_contents_type_code CHAR(4) NOT NULL,  -- �ե����륳��ƥ�ļ��̥����� (P) (F)
	file_contents_value TEXT,                  -- �ե����륳��ƥ����
	CONSTRAINT file_contents_pkey PRIMARY KEY (file_id, file_contents_type_code),
	CONSTRAINT file_contents_fkey1 FOREIGN KEY (file_id) REFERENCES file_detail_info (file_id) ON DELETE CASCADE,
	CONSTRAINT file_contents_fkey2 FOREIGN KEY (file_contents_type_code) REFERENCES file_contents_type_master (file_contents_type_code)
);

-- �ե���������
CREATE TABLE file_history (
	file_history_id BIGINT NOT NULL,               -- �ե���������ID (P)
	file_id BIGINT NOT NULL,                       -- �ե�����ID (F)
	display_file_name TEXT NOT NULL,               -- �ե�����ɽ��̾
	server_file_name TEXT NOT NULL,                -- �����Хե�����̾
	thumbnail_server_file_name TEXT,               -- ����ͥ��륵���Хե�����̾
	mime_type TEXT,                                -- Mime Type
	file_size INTEGER,                             -- �ե����륵����
	update_date TIMESTAMP(0) NOT NULL,              -- ��Ͽ��
	update_user_community_id BIGINT,                -- ��Ͽ�桼�����ߥ�˥ƥ�ID
	file_history_operation_code CHAR(4) NOT NULL,  -- �ե��������������� (F)
	CONSTRAINT file_history_pkey PRIMARY KEY (file_history_id),
	CONSTRAINT file_history_fkey1 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE,
	CONSTRAINT file_history_fkey2 FOREIGN KEY (file_history_operation_code) REFERENCES file_history_operation_master (file_history_operation_code)
);

CREATE SEQUENCE file_history_id_seq;

-- �ե��������򥳥���
CREATE TABLE file_history_comment (
	file_history_comment_id BIGINT NOT NULL,                    -- �ե��������򥳥���ID (P)
	file_history_id BIGINT NOT NULL,                            -- �ե���������ID (F)
	user_community_id BIGINT NOT NULL,                          -- ��ƼԤΥ桼�����ߥ�˥ƥ�ID
	comment TEXT,                                               -- ������
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- �����
	CONSTRAINT file_history_comment_pkey PRIMARY KEY (file_history_comment_id),
	CONSTRAINT file_history_comment_fkey1 FOREIGN KEY (file_history_id) REFERENCES file_history (file_history_id) ON DELETE CASCADE
);

CREATE SEQUENCE file_history_comment_id_seq;

-- �ե����륢����������
CREATE TABLE file_access_history (
	user_community_id BIGINT NOT NULL,  -- ���������ԤΥ桼�����ߥ�˥ƥ�ID (P)(F)
	file_id BIGINT NOT NULL,            -- ���������оݤ�file_id (P)(F)
	access_date TIMESTAMP(0) NOT NULL,  -- ������������
	CONSTRAINT file_access_history_pkey PRIMARY KEY (user_community_id, file_id),
	CONSTRAINT file_access_history_fkey1 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT file_access_history_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);

-- �ե������������ơ��֥�
CREATE TABLE file_public_access (
	file_id BIGINT NOT NULL,                               				-- �ե�����ID
	folder_id BIGINT NOT NULL,                             				-- �ե������ID
	community_id BIGINT NOT NULL,                          				-- ���ߥ�˥ƥ�ID
	access_code TEXT,                  									-- ��������������
	all_access_count BIGINT NOT NULL DEFAULT 0,            				-- ����������
	access_count BIGINT NOT NULL DEFAULT 0,            					-- ����������
	access_start_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP, 	-- ����������������
	update_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,        -- ��������
	CONSTRAINT file_public_access_pkey PRIMARY KEY (file_id),
	CONSTRAINT file_public_access_fkey1 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);

--------------------------------
-- ���ߥ�˥ƥ�
--------------------------------

-- �Ǽ���
CREATE TABLE bbs (
	bbs_id BIGINT NOT NULL,                                     -- ���ID (P)
	community_id BIGINT NOT NULL,                               -- ���ߥ�˥ƥ�ID (F)
	user_community_id BIGINT NOT NULL,                          -- ��ƼԤΥ��ߥ�˥ƥ�ID(�ޥ��ڡ���)
	subject TEXT,                                               -- ��̾
	body TEXT NOT NULL,                                         -- ����
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- �������
	open_level_code CHAR(2) NOT NULL,                           -- ������٥륳����
	expire_date TIMESTAMP(0),                                   -- �Ǻܽ�λ��
	bbs_delete_flag BOOLEAN NOT NULL DEFAULT 'f',               -- ����ե饰
	ml_send_flag BOOLEAN NOT NULL DEFAULT 'f',                  -- ML�����ե饰
	CONSTRAINT bbs_pkey PRIMARY KEY (bbs_id),
	CONSTRAINT bbs_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT bbs_fkey2 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);
CREATE SEQUENCE bbs_id_seq;

-- �Ǽ����ֿ�
CREATE TABLE bbs_res (
	bbs_res_id BIGINT NOT NULL,                                 -- �ֿ����ID (P)
	bbs_id BIGINT NOT NULL,                                     -- ���ID(�Ƶ���) (F)
	user_community_id BIGINT NOT NULL,                          -- ��ƼԤΥ��ߥ�˥ƥ�ID(�ޥ��ڡ���)
	subject TEXT,                                               -- ��̾
	body TEXT NOT NULL,                                         -- ����
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- �������
	bbs_res_delete_flag BOOLEAN NOT NULL DEFAULT 'f',           -- ����ե饰
	CONSTRAINT bbs_res_pkey PRIMARY KEY (bbs_res_id),
	CONSTRAINT bbs_res_fkey1 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE
);
CREATE SEQUENCE bbs_res_id_seq;

-- �Ǽ��Ŀ���Ѥߥ��ߥ�˥ƥ�
CREATE TABLE bbs_trusted_community (
	bbs_id BIGINT NOT NULL,                -- �Ǽ������ID
	trusted_community_id BIGINT NOT NULL,  -- ����Ѥߥ��ߥ�˥ƥ�ID
	CONSTRAINT bbs_trusted_community_pkey PRIMARY KEY (bbs_id, trusted_community_id),
	CONSTRAINT bbs_trusted_community_fkey1 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE,
	CONSTRAINT bbs_trusted_community_fkey2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- �Ǽ��ĥ�����������
CREATE TABLE bbs_access_history (
	user_community_id BIGINT NOT NULL,  -- ���������ԤΥ桼�����ߥ�˥ƥ�ID
	bbs_id BIGINT NOT NULL,             -- ���������оݤ�bbs_id
	access_date TIMESTAMP(0) NOT NULL,  -- ������������
	CONSTRAINT bbs_access_history_pkey PRIMARY KEY (user_community_id, bbs_id),
	CONSTRAINT bbs_access_history_fkey1 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT bbs_access_history_fkey2 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE
);

-- (�Ǽ���)����RSS
CREATE TABLE external_rss (
	bbs_id BIGINT NOT NULL,               -- bbs_id (P) (F)
	rss_url TEXT NOT NULL,                -- RSS�����URL
	rss_channel_title TEXT NOT NULL,      -- RSS�����ͥ륿���ȥ�
	rss_item_title TEXT NOT NULL,         -- RSS�����ƥ�title
	rss_item_content TEXT NOT NULL,       -- RSS�����ƥ�content
	rss_item_date TIMESTAMP(0),           -- RSS�����ƥ�date
	rss_item_link TEXT,                   -- RSS�����ƥ�link
	CONSTRAINT external_rss_pkey PRIMARY KEY (bbs_id),
	CONSTRAINT external_rss_fkey1 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE
);

-- �������塼��
CREATE TABLE schedule (
	schedule_id BIGINT NOT NULL,                      -- �������塼��ID (P)
	community_id BIGINT NOT NULL,                     -- ���ߥ�˥ƥ�ID (F)
	user_community_id BIGINT NOT NULL,                -- �����Υ��ߥ�˥ƥ�ID(�ޥ��ڡ���)
	schedule_name TEXT NOT NULL,                      -- �������塼��̾
	schedule_place TEXT NOT NULL,                     -- ���
	schedule_detail TEXT,                             -- �ܺپ���
	schedule_closing_datetime TIMESTAMP(0) NOT NULL,  -- �������
	schedule_target_kind VARCHAR(4) NOT NULL DEFAULT 'ALL',    -- �оݼ���('ALL'/'FREE')
	decide_adjustment_date_id BIGINT NOT NULL DEFAULT 0,       -- �������塼���������ID
	entry_datetime TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- ��������
	update_datetime TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP, -- ��������

	CONSTRAINT schedule_pkey PRIMARY KEY (schedule_id),
	CONSTRAINT schedule_fkey1 FOREIGN KEY (community_id) 
			REFERENCES community (community_id) ON DELETE CASCADE
);
CREATE SEQUENCE schedule_id_seq;

-- �������塼���������
CREATE TABLE schedule_adjustment_dates (
	schedule_id BIGINT NOT NULL,                    -- �������塼��ID (P)(F)
	adjustment_date_id BIGINT NOT NULL,             -- �������塼���������ID (P)
	adjustment_date_string TEXT NOT NULL,           -- �������塼���������
	adjustment_date_delete_flag BOOLEAN NOT NULL DEFAULT 'f', 
													-- ����ե饰(�����TRUE)
	CONSTRAINT schedule_adjustment_dates_pkey PRIMARY KEY (schedule_id,adjustment_date_id),
	CONSTRAINT schedule_adjustment_dates_fkey1 FOREIGN KEY (schedule_id) 
			REFERENCES schedule (schedule_id) ON DELETE CASCADE
);
CREATE SEQUENCE adjustment_date_id_seq;

-- �������塼����������
CREATE TABLE schedule_answer_selection (
	schedule_id BIGINT NOT NULL,                    -- �������塼��ID (P)(F)
	answer_no BIGINT NOT NULL,                      -- �������塼������ֹ� (P)
	answer_char VARCHAR(1),        			        -- �������塼���������
	answer_score SMALLINT,  		                -- �������塼�����������
	answer_detail TEXT,                             -- �������塼���������
	answer_default BOOLEAN NOT NULL DEFAULT 'f',    -- �������塼���������ͥե饰
	CONSTRAINT schedule_answer_selection_pkey PRIMARY KEY (schedule_id,answer_no),
	CONSTRAINT schedule_answer_selection_fkey1 FOREIGN KEY (schedule_id) 
			REFERENCES schedule (schedule_id) ON DELETE CASCADE
);

-- �������塼�뻲��
CREATE TABLE schedule_participant (
	schedule_id BIGINT NOT NULL,                    -- �������塼��ID (P)(F)
	user_community_id BIGINT NOT NULL,              -- �����ԤΥ��ߥ�˥ƥ�ID(�ޥ��ڡ���)(P)
	participant_comment TEXT,                       -- ���å�����
	participant_delete_flag BOOLEAN NOT NULL DEFAULT 'f', -- ����ե饰(���äȤ����=TRUE)
	CONSTRAINT schedule_participant_pkey PRIMARY KEY (schedule_id,user_community_id),
	CONSTRAINT schedule_participant_fkey1 FOREIGN KEY (schedule_id) 
			REFERENCES schedule (schedule_id) ON DELETE CASCADE
);

-- �������塼�����
CREATE TABLE schedule_answer (
	schedule_id BIGINT NOT NULL,                    -- �������塼��ID (P)(F)
	user_community_id BIGINT NOT NULL,              -- �����ԤΥ��ߥ�˥ƥ�ID(�ޥ��ڡ���)(P)
	adjustment_date_id BIGINT NOT NULL,             -- �������塼���������ID (P)(F)
	answer_no BIGINT NOT NULL,                      -- �������塼������ֹ� (P)
	CONSTRAINT schedule_answer_pkey PRIMARY KEY (
			schedule_id,user_community_id,adjustment_date_id,answer_no),
	CONSTRAINT schedule_answer_fkey1 FOREIGN KEY (schedule_id) 
			REFERENCES schedule (schedule_id) ON DELETE CASCADE,
	CONSTRAINT schedule_answer_fkey2 FOREIGN KEY (schedule_id,adjustment_date_id)
			REFERENCES schedule_adjustment_dates (schedule_id,adjustment_date_id) 
			ON DELETE CASCADE
);


--------------------------------
-- �ƥ��åץ��ɥե�����
--------------------------------

-- ���ߥ�˥ƥ������ե�����
CREATE TABLE community_image_file (
	community_id BIGINT NOT NULL,  -- ���ߥ�˥ƥ�ID (P) (F)
	file_id BIGINT NOT NULL,       -- �ե�����ID (P) (F)
	file_id_ol01 bigint,           -- ������٥�01�ѥե�����ID
	file_id_ol02 bigint,           -- ������٥�02�ѥե�����ID
	file_id_ol05 bigint,           -- ������٥�05�ѥե�����ID
	CONSTRAINT community_image_file_pkey PRIMARY KEY (community_id, file_id),
	CONSTRAINT community_image_file_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT community_image_file_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE,
	CONSTRAINT community_image_file_fkey3 FOREIGN KEY (file_id_ol01) REFERENCES file_info (file_id) ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT community_image_file_fkey4 FOREIGN KEY (file_id_ol02) REFERENCES file_info (file_id) ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT community_image_file_fkey5 FOREIGN KEY (file_id_ol05) REFERENCES file_info (file_id) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- �������꡼�ե�����
CREATE TABLE diary_file (
	diary_id BIGINT NOT NULL,  -- �������꡼ID (P) (F)
	file_id BIGINT NOT NULL,   -- �ե�����ID (P) (F)
	CONSTRAINT diary_file_pkey PRIMARY KEY (diary_id, file_id),
	CONSTRAINT diary_file_fkey1 FOREIGN KEY (diary_id) REFERENCES diary (diary_id) ON DELETE CASCADE,
	CONSTRAINT diary_file_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);

-- �Ǽ��ĥե�����
CREATE TABLE bbs_file (
	bbs_id BIGINT NOT NULL,   -- �Ǽ���ID (P) (F)
	file_id BIGINT NOT NULL,  -- �ե�����ID (P) (F)
	CONSTRAINT bbs_file_pkey PRIMARY KEY (bbs_id, file_id),
	CONSTRAINT bbs_file_fkey1 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE,
	CONSTRAINT bbs_file_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);


--------------------------------
-- �桼������
--------------------------------

-- �桼������
CREATE TABLE user_info (
	user_id TEXT NOT NULL,                            -- �桼��ID (P)
	user_community_id BIGINT NOT NULL,                -- ���ߥ�˥ƥ�ID(�ޥ��ڡ���) (F)
	administrator_flag BOOLEAN NOT NULL DEFAULT 'f',  -- �����ƥ�����ԥե饰
	CONSTRAINT user_info_pkey PRIMARY KEY (user_id, user_community_id),
	CONSTRAINT user_info_fkey1 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);


--------------------------------
-- ������
--------------------------------

-- ���ޥ���
CREATE TABLE operation_master (
	operation_code CHAR(4) NOT NULL,  -- ������ (P)
	operation_name TEXT NOT NULL,     -- ���̾
	CONSTRAINT operation_master_en_pkey PRIMARY KEY (operation_code)
);

-- #### �� ���ܸ�ǡ��� #### --
-- COPY operation_master FROM stdin;
-- 0101	������
-- 0201	�桼��������Ͽ
-- 0202	LDAP�桼��������Ͽ
-- 0203	�桼�������ѹ�
-- 0204	�桼�����
-- 0301	�����ƥ������ѹ�
-- \.

-- #### �Ѹ��� ####
COPY operation_master FROM stdin;
0101	Login
0201	New User Registration
0202	New LDAP User Registration
0203	Change User Information
0204	Remove User
0301	Change System Settings
\.


-- ��
CREATE TABLE log (
	log_id BIGINT NOT NULL,                                    -- ��ID (P)
	log_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- ������
	user_id TEXT NOT NULL,                                     -- �桼��ID
	user_name TEXT NOT NULL,                                   -- ��̾
	user_community_id BIGINT NOT NULL,                         -- �桼�����ߥ�˥ƥ�ID
	community_name TEXT,                                       -- �˥å��͡���
	administrator_flag BOOLEAN NOT NULL,                       -- �����ƥ�����ԥե饰
	operation_code CHAR(4) NOT NULL,                           -- ������
	operation_result BOOLEAN NOT NULL,                         -- ����� (t=����, f=����)
	message TEXT,                                              -- ��å�����(�������)
	CONSTRAINT log_pkey PRIMARY KEY (log_id),
	CONSTRAINT log_fkey1 FOREIGN KEY (operation_code) REFERENCES operation_master (operation_code)
);

CREATE SEQUENCE log_id_seq;

-- ­��
CREATE TABLE footprint (
	community_id BIGINT NOT NULL,                               -- ­�פ��դ���줿�桼��
	visitor_community_id BIGINT NOT NULL,                       -- ­�פ��դ����桼��
	contents_type_code CHAR(2) NOT NULL,                        -- (21=�������꡼, 33=�ե�����[����]) 
	contents_title TEXT NOT NULL,                               -- ���̾
	contents_link_url TEXT NOT NULL,                            -- ���URL
	contents_date TIMESTAMP(0) NOT NULL,                        -- ­�פ��դ��������Υ���ƥ�Ĥ�����
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- ­������
	CONSTRAINT footprint_fkey1 FOREIGN KEY (contents_type_code) REFERENCES contents_type_master (contents_type_code)
);

--------------------------------
-- �����ƥफ��Τ��Τ餻
--------------------------------

-- �����ƥफ��Τ��Τ餻
CREATE TABLE system_announce (
	system_announce_id BIGINT NOT NULL,                         -- �����ƥॢ�ʥ���ID (P)
	user_community_id BIGINT NOT NULL,                          -- ��Ƥ�Ԥä��桼�����ߥ�˥ƥ�ID
	subject TEXT NOT NULL,                                      -- ��̾
	body TEXT NOT NULL,                                         -- ��ʸ
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- �������
	expire_date DATE,                                           -- �Ǻܽ�λ��
	system_announce_delete_flag BOOLEAN NOT NULL DEFAULT 'f',   -- ����ե饰
	CONSTRAINT system_announce_pkey PRIMARY KEY (system_announce_id)
);

CREATE SEQUENCE system_announce_id_seq;


--------------------------------
-- �����ƥ�����
--------------------------------

-- �����ƥ����ꥰ�롼��
CREATE TABLE system_config_group (
	system_config_group_code CHAR(2) NOT NULL,  -- ���롼�ץ����� (P)
	system_config_group_name TEXT NOT NULL,     -- ���롼��̾
	display_order INTEGER NOT NULL,             -- ɽ�����
	CONSTRAINT system_config_group_pkey PRIMARY KEY (system_config_group_code)
);

-- �����ƥ�����
CREATE TABLE system_config (
	system_config_group_code CHAR(2) NOT NULL,  -- ���롼�ץ�����
	keyword TEXT NOT NULL,                      -- ������� (P)
	value TEXT NOT NULL,                        -- ��
	type TEXT NOT NULL,                         -- �� (number or string)
	display_order INTEGER NOT NULL,             -- ɽ�����
	CONSTRAINT system_config_pkey PRIMARY KEY (keyword),
	CONSTRAINT system_config_fkey1 FOREIGN KEY (system_config_group_code) REFERENCES system_config_group (system_config_group_code) ON DELETE CASCADE
);

COPY system_config_group FROM stdin;
01	�����ƥ�	1
02	�ޥ��ڡ���	2
03	���ߥ�˥ƥ�	3
04	�����ե�����	4
05	��󥭥�	5
06	�ѥ֥�å���꡼��	6
07	��	7
08	�桼������	8
\.

COPY system_config FROM stdin;
01	SYSTEM_NAME	�����ǥߥå����ߥ�˥ƥ������ƥ�	string	1
01	SYSTEM_OUTLINE	�����ǥߥå����ߥ�˥ƥ������ƥ�Ǥ�	string	2
01	SYSTEM_BASE_URL	http://xxx.yyy.zz.jp/	string	3
01	SYSTEM_BASE_LOGIN_URL	https://xxx.yyy.zz.jp/login/	string	4
01	SYSTEM_MAIL_ADDR	acs-admin@xxx.yyy.zz.jp	string	5
01	SMTP_SERVER	localhost	string	6
01	SMTP_PORT	25	number	7
01	LDAP_SERVER	ldap.yyy.zz.jp	string	8
01	LDAP_PORT	51000	number	9
01	LDAP_BASE_DN	o=ZZZZ-UNIV	string	10
01	LDAP_BIND_DN	uid=ZZ-ACS,ou=DptStaff,o=ZZZZ-UNIV	string	11
01	LDAP_BIND_PASSWD	**********	password	12
01	DESIGN_STYLE_CSS_URL	http://xxx.yyy.zz.jp/css/selection	string	13
02	NEW_INFO_TOP_DISPLAY_MAX_COUNT	5	number	1
02	NEW_INFO_LIST_DISPLAY_MAX_COUNT	20	number	2
02	FRIENDS_DISPLAY_MAX_COUNT	12	number	3
02	COMMUNITY_DISPLAY_MAX_COUNT	12	number	4
02	USER_SEARCH_RESULT_DISPLAY_MAX_COUNT	20	number	5
02	DIARY_RSS_TERM	14	number	6
02	NEW_INFO_TOP_TERM	20	number0	7
02	NEW_INFO_LIST_TERM	60	number0	8
02	FOOTPRINT_LIST_TERM	30	number	9
02	FOOTPRINT_LIST_DISPLAY_MAX_COUNT	20	number	10
03	COMMUNITY_MEMBER_DISPLAY_MAX_COUNT	12	number	1
03	COMMUNITY_SEARCH_RESULT_DISPLAY_MAX_COUNT	20	number	2
03	COMMUNITY_SCHEDULE_LIST_DISPLAY_MAX_COUNT	10	number	3
03	COMMUNITY_ML_MAIL_ADDR	bbs@xxx.yyy.zz.jp	string	4
03	COMMUNITY_ML_POP_SERVER	localhost	string	5
03	COMMUNITY_ML_POP_PORT	110	number	6
03	COMMUNITY_ML_POP_USER	acsuser	string	7
03	COMMUNITY_ML_POP_PASSWD	**********	password	8
03	BBS_RSS_TERM	14	number	9
04	PROFILE_IMAGE_WIDTH_MAX	180	number	1
04	PROFILE_IMAGE_HEIGHT_MAX	180	number	2
04	PROFILE_IMAGE_THUMB_WIDTH_MAX	76	number	3
04	PROFILE_IMAGE_THUMB_HEIGHT_MAX	76	number	4
04	BBS_IMAGE_WIDTH_MAX	1280	number	5
04	BBS_IMAGE_HEIGHT_MAX	960	number	6
04	BBS_IMAGE_THUMB_WIDTH_MAX	120	number	7
04	BBS_IMAGE_THUMB_HEIGHT_MAX	120	number	8
04	BBS_IMAGE_RSS_WIDTH_MAX	200	number	9
04	BBS_IMAGE_RSS_HEIGHT_MAX	200	number	10
04	DIARY_IMAGE_WIDTH_MAX	1280	number	11
04	DIARY_IMAGE_HEIGHT_MAX	960	number	12
04	DIARY_IMAGE_THUMB_WIDTH_MAX	120	number	13
04	DIARY_IMAGE_THUMB_HEIGHT_MAX	120	number	14
04	FOLDER_IMAGE_THUMB_WIDTH_MAX	76	number	17
04	FOLDER_IMAGE_THUMB_HEIGHT_MAX	76	number	18
05	USER_RANKING_COUNT_TERM	30	number	1
05	COMMUNITY_RANKING_COUNT_TERM	30	number	2
06	RSS_DISPLAY_MAX_COUNT	120	number	1
07	LOG_DISPLAY_MAX_COUNT	50	number	1
08	GET_LOGOUT_DATE_EVERYTIME	1	select	1
08	DISPLAY_PHOTOS_FOR_PUBLIC	1	select	2
08	NAME_DISPLAY_LEVEL	02	select	3
\.


--------------------------------
-- ���������б�
--------------------------------

-- ���������
CREATE TABLE login_info
(
	logout_id integer NOT NULL,
	community_id integer NOT NULL,
	login_date TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP NOT NULL,
	logout_date TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP,
	use_button_flg boolean DEFAULT false,
	CONSTRAINT "PKEY_LOGIN_INFO" PRIMARY KEY (logout_id)
);
CREATE SEQUENCE login_id_seq;



--------------------------------
-- ��å�������ǽ
--------------------------------

-- ��å�����
CREATE TABLE message
(
	message_id bigint NOT NULL,
	subject text NOT NULL,
	body text,
	post_date timestamp(0) without time zone NOT NULL DEFAULT now(),
	CONSTRAINT message_pkey PRIMARY KEY (message_id)
) ;
CREATE SEQUENCE message_id_seq;

-- ��å�����������
CREATE TABLE message_receiver
(
	message_receiver_id bigint NOT NULL,
	message_id bigint NOT NULL,
	community_id bigint NOT NULL,
	read_flag boolean NOT NULL DEFAULT false,
	message_delete_flag boolean NOT NULL DEFAULT false,
	CONSTRAINT message_receiver_pkey PRIMARY KEY (message_receiver_id),
	CONSTRAINT message_receiver_fkey1 FOREIGN KEY (message_id)
		REFERENCES message (message_id) 
		ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT message_receiver_fkey2 FOREIGN KEY (community_id)
		REFERENCES community (community_id) 
		ON UPDATE NO ACTION ON DELETE CASCADE
);
CREATE SEQUENCE message_receiver_id_seq;

-- ��å�����������
CREATE TABLE message_sender
(
	message_sender_id bigint NOT NULL,
	message_id bigint NOT NULL,
	community_id bigint NOT NULL,
	message_delete_flag boolean NOT NULL DEFAULT false,
	CONSTRAINT messsage_sender_pkey PRIMARY KEY (message_sender_id),
	CONSTRAINT message_sender_fkey1 FOREIGN KEY (message_id)
		REFERENCES message (message_id) 
		ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT message_sender_fkey2 FOREIGN KEY (community_id)
		REFERENCES community (community_id) 
		ON UPDATE NO ACTION ON DELETE CASCADE
);
CREATE SEQUENCE message_sender_id_seq;

----------------------------------------------------------------
-- ��ǽ�����ѥ���ǥå���
----------------------------------------------------------------
--
-- �����󥫥��Υ���ǥå�������
--
CREATE INDEX diary_post_date ON diary (post_date);
CREATE INDEX diary_comment_post_date ON diary_comment (post_date);
CREATE INDEX bbs_post_date ON bbs (post_date);
CREATE INDEX bbs_res_post_date ON bbs_res (post_date);
CREATE INDEX file_info_entry_date ON file_info (entry_date);
CREATE INDEX file_info_update_date ON file_info (update_date);

----------------------------------------------------------------
-- �ӥ塼
----------------------------------------------------------------
--
-- SQL Function���Ѥ��ӥ塼�κ���
--
CREATE VIEW acs_view_bbs_last_timestamp AS
SELECT
  bbs.bbs_id,
  CASE
    WHEN bbsres.last_post_date is null THEN bbs.post_date
    WHEN bbs.post_date >= bbsres.last_post_date THEN bbs.post_date
    ELSE bbsres.last_post_date
  END AS bbs_last_timestamp
FROM bbs LEFT JOIN
  (SELECT bbs_id, MAX(post_date) AS last_post_date FROM bbs_res GROUP BY bbs_id) AS bbsres
ON bbs.bbs_id = bbsres.bbs_id
;

----------------------------------------------------------------
-- �桼������ؿ�
----------------------------------------------------------------

----------------------------------------------------
-- �ؿ�: TIMESTAMP��YYYY/MM/DD(wday) H:MM���Ѵ�����
--     $1:
--         YYYY/MM/DD => 2005/01/02
--         FMMM/FMDD  => 1/2
--     $2:
--         FMHH24:MI:SS   => 1:02:03
--         FMHH24:MI      => 1:02
----------------------------------------------------
CREATE FUNCTION acs_convert_timestamp_ja(TIMESTAMP, TEXT, TEXT)
RETURNS TEXT AS '
  SELECT
    to_char($1, $2 || ''('' || jday || '') '' || $3)::TEXT
  FROM
    (SELECT
       CASE
         WHEN wday = ''1'' THEN ''��''
         WHEN wday = ''2'' THEN ''��''
         WHEN wday = ''3'' THEN ''��''
         WHEN wday = ''4'' THEN ''��''
         WHEN wday = ''5'' THEN ''��''
         WHEN wday = ''6'' THEN ''��''
         WHEN wday = ''7'' THEN ''��''
       END AS jday
     FROM
       (SELECT to_char($1, ''D'') AS wday) AS dummy1
    ) AS dummy2
' LANGUAGE 'sql';

-- 2�Ĥλ������Ӥ��ƺǿ��������������
CREATE FUNCTION acs_get_last_timestamp(TIMESTAMP(0), TIMESTAMP(0))
RETURNS TIMESTAMP(0) AS '
  SELECT
    CASE
      WHEN $2 is null THEN $1
      WHEN $1 >= $2 THEN $1
      ELSE $2
    END AS ret_timestamp
' LANGUAGE 'sql';


-- �������꡼ID����ꤷ�ƥ����Ȥη�����������
CREATE FUNCTION acs_get_diary_comment_num(BIGINT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM diary_comment
    WHERE diary_id = $1
' LANGUAGE 'sql';

-- �������꡼ID����ꤷ�ƺǽ����������������� (�Ƶ����ȥ����Ȥ������������Ӥ���)
CREATE FUNCTION acs_get_diary_last_post_date(BIGINT)
RETURNS TIMESTAMP(0) AS '
  SELECT acs_get_last_timestamp(diary.post_date, diary_comment.post_date) as diary_last_post_date
    FROM diary LEFT OUTER JOIN diary_comment USING(diary_id)
    WHERE diary.diary_id = $1
    ORDER BY diary_last_post_date DESC
    LIMIT 1
' LANGUAGE 'sql';

-- �������꡼ID����ꤷ�ƥ������꡼�����Ȥκǽ����������������� (�����Ȥ�0��ξ���null)
-- 31ʸ������: comment �� c ��û��
CREATE FUNCTION acs_get_diary_c_last_post_date(BIGINT)
RETURNS TIMESTAMP(0) AS '
  SELECT diary_comment.post_date
    FROM diary, diary_comment
    WHERE diary.diary_id = $1
      AND diary.diary_id = diary_comment.diary_id
    ORDER BY diary_comment.post_date DESC
    LIMIT 1
' LANGUAGE 'sql';

-- �桼�����ߥ�˥ƥ�ID�ȥ������꡼ID����ꤷ��̤�ɤ��ɤ���Ƚ�̤���
CREATE FUNCTION acs_is_unread_diary(BIGINT, BIGINT)
RETURNS BOOLEAN AS '
  SELECT EXISTS (
    SELECT *
      FROM (diary LEFT OUTER JOIN diary_access_history
                  ON diary.diary_id = diary_access_history.diary_id AND diary_access_history.user_community_id = $1)
      WHERE diary.diary_id = $2
        AND (diary_access_history.access_date is null OR diary.post_date > diary_access_history.access_date)
  )
' LANGUAGE 'sql';

-- �桼�����ߥ�˥ƥ�ID�ȥ������꡼ID����ꤷ�ƥ������꡼�����Ȥ�̤�ɤ��ɤ���Ƚ�̤���
CREATE FUNCTION acs_is_unread_diary_comment(BIGINT, BIGINT)
RETURNS BOOLEAN AS '
  SELECT EXISTS (
    SELECT *
      FROM (diary LEFT OUTER JOIN diary_access_history
                  ON diary.diary_id = diary_access_history.diary_id AND diary_access_history.user_community_id = $1)
      WHERE diary.diary_id = $2
        AND (diary_access_history.access_date is null OR acs_get_diary_last_post_date(diary.diary_id) > diary_access_history.access_date)
  )
' LANGUAGE 'sql';

-- �桼�����ߥ�˥ƥ�ID�ȥե�����ID����ꤷ�ƥե����뤬̤�ɤ��ɤ���Ƚ�̤���
CREATE FUNCTION acs_is_unread_file(BIGINT, BIGINT)
RETURNS BOOLEAN AS '
  SELECT EXISTS (
    SELECT *
      FROM (file_info LEFT OUTER JOIN file_access_history
                  ON file_info.file_id = file_access_history.file_id AND file_access_history.user_community_id = $1)
      WHERE file_info.file_id = $2
        AND (file_access_history.access_date is null OR file_info.update_date > file_access_history.access_date)
  )
' LANGUAGE 'sql';


-- bbs_id����ꤷ���ֿ��η�����������
CREATE FUNCTION acs_get_bbs_res_num(BIGINT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM bbs_res
    WHERE bbs_res.bbs_id = $1
' LANGUAGE 'sql';

-- bbs_id����ꤷ�ƺǽ����������������� (�Ƶ������ֿ������������������Ӥ���)
CREATE FUNCTION acs_get_bbs_last_post_date(BIGINT)
RETURNS TIMESTAMP(0) AS '
  SELECT acs_get_last_timestamp(bbs.post_date, bbs_res.post_date) as bbs_last_post_date
    FROM bbs LEFT OUTER JOIN bbs_res USING(bbs_id)
    WHERE bbs.bbs_id = $1
    ORDER BY bbs_last_post_date DESC
    LIMIT 1
' LANGUAGE 'sql';

-- �桼�����ߥ�˥ƥ�ID�ȥ������꡼ID����ꤷ�ƥ���åɤ�̤�ɤ��ɤ���Ƚ�̤���
CREATE FUNCTION acs_is_unread_bbs(BIGINT, BIGINT)
RETURNS BOOLEAN AS '
  SELECT EXISTS (
    SELECT *
      FROM (bbs LEFT OUTER JOIN bbs_access_history
                  ON bbs.bbs_id = bbs_access_history.bbs_id AND bbs_access_history.user_community_id = $1)
      WHERE bbs.bbs_id = $2
        AND (bbs_access_history.access_date is null OR acs_get_bbs_last_post_date(bbs.bbs_id) > bbs_access_history.access_date)
  )
' LANGUAGE 'sql';


-- �桼���Υޥ��ե�󥺿����������
CREATE FUNCTION acs_get_friends_num(BIGINT)
RETURNS BIGINT AS ' 
  SELECT count(*)
    FROM community, sub_community, community as FRIENDS_COMMUNITY, community_type_master, community_member,
         community as USER_COMMUNITY
    WHERE community.community_id = $1
      AND community.community_id = sub_community.community_id
      AND sub_community.sub_community_id = FRIENDS_COMMUNITY.community_id
      AND FRIENDS_COMMUNITY.community_type_code = community_type_master.community_type_code
      AND community_type_master.community_type_name = ''�ޥ��ե��''
      AND FRIENDS_COMMUNITY.community_id = community_member.community_id
      AND community_member.user_community_id = USER_COMMUNITY.community_id
      AND USER_COMMUNITY.delete_flag != ''t''
' LANGUAGE 'sql';

-- �桼���Υޥ����ߥ�˥ƥ������������
CREATE FUNCTION acs_get_community_num(BIGINT)
RETURNS BIGINT AS ' 
  SELECT count(*)
    FROM community, community_type_master, community_member
    WHERE community.community_type_code = community_type_master.community_type_code
      AND community.community_id = community_member.community_id
      AND community_type_master.community_type_name = ''���ߥ�˥ƥ�''
      AND community_member.user_community_id = $1
      AND community.delete_flag != ''t''
' LANGUAGE 'sql';

-- ���ߥ�˥ƥ��Υ��п����������
CREATE FUNCTION acs_get_community_member_num(BIGINT)
RETURNS BIGINT AS ' 
  SELECT count(*)
    FROM community_member, community as USER_COMMUNITY
    WHERE community_member.community_id = $1
      AND community_member.user_community_id = USER_COMMUNITY.community_id
      AND USER_COMMUNITY.delete_flag != ''t''
' LANGUAGE 'sql';

-- $1��$2���ߥ�˥ƥ��Υ��Ф��ɤ����������� (t/f)
CREATE FUNCTION acs_is_community_member(BIGINT, BIGINT)
RETURNS BOOLEAN AS '
  SELECT EXISTS (
    SELECT *
      FROM community, community_member
      WHERE community.community_id = community_member.community_id
        AND community.community_id = $2
        AND community_member.user_community_id = $1
  )
' LANGUAGE 'sql';


-- ��󥭥� --
-- �桼�����ߥ�˥ƥ�ID($1)����Ƥ���diary�ơ��֥���򽸷פ���
CREATE FUNCTION acs_get_diary_score(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM diary
    WHERE diary.community_id = $1
      AND diary.diary_delete_flag != ''t''
      AND diary.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
' LANGUAGE 'sql';

-- �桼�����ߥ�˥ƥ�ID($1)����Ƥ���diary_comment�ơ��֥���򽸷פ���
CREATE FUNCTION acs_get_diary_comment_score(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM diary, diary_comment
    WHERE diary.diary_delete_flag != ''t''
      AND diary.diary_id = diary_comment.diary_id
      AND diary_comment.user_community_id = $1
      AND diary_comment.diary_comment_delete_flag != ''t''
      AND diary_comment.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
' LANGUAGE 'sql';

-- �桼�����ߥ�˥ƥ�ID($1)����Ƥ��줿diary_comment�ơ��֥���򽸷פ���
CREATE FUNCTION acs_get_commented_diary_score(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM diary, diary_comment
    WHERE diary.community_id = $1
      AND diary.diary_id = diary_comment.diary_id
      AND diary_comment.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
      AND diary.diary_delete_flag != ''t''
      AND diary_comment.diary_comment_delete_flag != ''t''
' LANGUAGE 'sql';


-- �桼�����ߥ�˥ƥ�ID($1)����Ƥ���bbs�ơ��֥���򽸷פ���
CREATE FUNCTION acs_get_bbs_score_by_u_c_id(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM bbs
    WHERE bbs.community_id = $1
      AND bbs.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
      AND bbs.bbs_delete_flag != ''t''
' LANGUAGE 'sql';

-- �桼�����ߥ�˥ƥ�ID($1)����Ƥ���bbs_res�ơ��֥���򽸷פ���
CREATE FUNCTION acs_get_bbs_res_score_by_u_c_id(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM bbs, bbs_res
    WHERE bbs.bbs_id = bbs_res.bbs_id
      AND bbs_res.user_community_id = $1
      AND bbs_res.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
      AND bbs.bbs_delete_flag != ''t''
      AND bbs_res.bbs_res_delete_flag != ''t''
' LANGUAGE 'sql';

-- ���ߥ�˥ƥ�ID($1)����Ƥ��줿bbs�ơ��֥���򽸷פ���
CREATE FUNCTION acs_get_bbs_score_by_c_id(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM bbs
    WHERE bbs.community_id = $1
      AND bbs.bbs_delete_flag != ''t''
      AND bbs.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
' LANGUAGE 'sql';

-- ���ߥ�˥ƥ�ID($1)����Ƥ��줿bbs_res�ơ��֥���򽸷פ���
CREATE FUNCTION acs_get_bbs_res_score_by_c_id(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM bbs, bbs_res
    WHERE bbs.community_id = $1
      AND bbs.bbs_delete_flag != ''t''
      AND bbs.bbs_id = bbs_res.bbs_id
      AND bbs_res.bbs_res_delete_flag != ''t''
      AND bbs_res.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
' LANGUAGE 'sql';

-- ���ߥ�˥ƥ�ID($1)��file_info�ơ��֥���򽸷פ���
CREATE FUNCTION acs_get_file_info_score(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM file_info
    WHERE file_info.owner_community_id = $1
      AND file_info.update_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
' LANGUAGE 'sql';


-- �����ƥॢ�ʥ��� --
-- �Ǻܴ����⤫�ɤ������������
CREATE FUNCTION acs_is_expire_date(DATE)
RETURNS BOOLEAN AS '
  SELECT (CURRENT_DATE > $1)::BOOLEAN
' LANGUAGE 'sql';


-- ���ߥå�
COMMIT;


----------------------------------------------------------------
--**************************************************************
-- �����ƥ�����Ծ���
--**************************************************************
BEGIN;

-- �����ƥ�����ԤΥ桼������
-- �ޥ��ڡ���(�桼�����ߥ�˥ƥ�)
INSERT INTO community (community_id, community_name, community_type_code) VALUES (1, '�����ƥ������', '10');
-- �桼������
INSERT INTO user_info (user_id, user_community_id, administrator_flag) VALUES ('admin', 1, 't');
-- ��̾
INSERT INTO contents (community_id, contents_type_code, contents_value, open_level_code) VALUES (1, '01', '�����ƥ������', '02');
-- �᡼�륢�ɥ쥹
INSERT INTO contents (community_id, contents_type_code, contents_value, open_level_code) VALUES (1, '02', null, '03');
-- ����
INSERT INTO contents (community_id, contents_type_code, contents_value, open_level_code) VALUES (1, '00', null, '01');
-- �ޥ��ե��
INSERT INTO contents (community_id, contents_type_code, contents_value, open_level_code) VALUES (1, '11', null, '01');
-- �ޥ��ե�󥺥��ߥ�˥ƥ�
INSERT INTO community (community_id, community_type_code) VALUES (2, '20');
INSERT INTO sub_community (community_id, sub_community_id) VALUES (1, 2);
-- ���������ֹ�γ����ͤ�3�˥��å�
SELECT setval ('community_id_seq', 3, false);

COMMIT;


----------------------------------------------------------------
--**************************************************************
-- ���ƥ������
--**************************************************************
BEGIN;

-- ���ƥ��ꥰ�롼�ץޥ���
COPY category_group_master FROM stdin;
0001	����
0002	����
0003	ʬ��
0004	�ȿ�
0005	���衦��������
0006	��̣�����
0007	����¾
\.

-- ���ƥ���ޥ���
COPY category_master FROM stdin;
0001	���漼	0001
0002	���楰�롼��	0001
0003	�ٶ���	0001
0004	��Ʊ����	0001
0005	�ץ�������	0001
0006	����	0001
0007	�ز�	0001
0008	����ݥ�����	0001
0009	�����	0001
0010	�������å�	0001
0011	�ֵ�	0002
0012	���ֵ̹�	0002
0013	��������	0002
0014	�ϡ��ɥ�����	0003
0015	���եȥ�����	0003
0016	�ͥåȥ��	0003
0017	�ǡ����١���	0003
0018	���르�ꥺ��	0003
0019	��������	0003
0020	��������������	0003
0021	��ǥ�������	0003
0022	�ѥ�����������	0003
0023	��������	0003
0024	������ǥ�	0003
0025	�׻���	0003
0026	���ѥ����ƥ�	0003
0027	������ϩ	0003
0028	�ȹ��ߥ����ƥ�	0003
0029	���ڥ졼�ƥ��󥰥����ƥ�	0003
0030	�����ƥ�����	0003
0031	�ץ���ߥ�	0003
0032	�����������	0003
0033	�������ƥ�	0003
0034	�μ��������	0003
0035	���ξ������	0003
0036	��Х���	0003
0037	��ӥ�����	0003
0038	����	0003
0039	ʡ��	0003
0040	���ߥ�˥��������	0003
0041	����å�	0003
0042	ǧ�βʳ�	0003
0043	��ǥ�������	0003
0044	���ߥ�졼�����	0003
0045	¿��ͳ�٥����ƥ�	0003
0046	ʪ������	0003
0047	��̿����	0003
0048	�絬�Ϸ׻�	0003
0049	��ذ���	0003
0050	���ذ���	0003
0051	��ʪ�ذ���	0003
0052	ʪ���ذ���	0003
0053	�����ǥ�	0003
0054	ʬ����Ĵ	0003
0055	��Ū�ٱ�	0003
0056	����ǥ�����	0003
0057	ů�ء�����	0003
0058	���ܰ���	0003
0059	����	0003
0060	����	0003
0061	����	0003
0062	�к�	0003
0063	ʸ��	0003
0064	ˡΧ	0003
0065	�Ҳ�	0003
0066	�򹯡��ݷ�	0003
0067	����	0004
0068	�ز�	0004
0069	�����	0004
0070	�칶	0004
0071	������	0004
0072	�Ѱ���	0004
0073	ʸ����	0005
0074	��ư��	0005
0075	�ܡ����	0006
0076	���ڡ��μ�	0006
0077	�ǲ衦�ƥ�ӡ����˥ᡦ��ͥ	0006
0078	������	0006
0079	�֡��Х���	0006
0080	ι��	0006
0081	���󥿡��ͥåȡ�����ԥ塼��	0006
0082	�ա��ɡ��쥹�ȥ��	0006
0083	�ե��å����	0006
0084	�ڥåȡ�ưʪ	0006
0085	���ݡ��ġ������ȥɥ�	0006
0086	��ʡ�����	0006
0087	��̣���������	0006
0088	����¾	0007
\.

COMMIT;


----------------------------------------------------------------


-- EOF
