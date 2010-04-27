----------------------------------------------------------------
-- アカデミックコミュニティシステム
-- $Id: setup.sql,v 1.77 2007/03/30 05:08:15 w-ota Exp $
----------------------------------------------------------------


-- トランザクション開始
BEGIN;


--------------------------------
-- マスタ
--------------------------------

-- コミュニティ種別マスタ
CREATE TABLE community_type_master (
	community_type_code CHAR(2) NOT NULL,  -- コミュニティ種別コード (P)
	community_type_name TEXT NOT NULL,     -- コミュニティ種別名
	CONSTRAINT community_type_master_pkey1 PRIMARY KEY (community_type_code)
);

COPY community_type_master FROM stdin;
10	マイページ
20	マイフレンズ
30	マイフレンズグループ
40	コミュニティ
\.

-- コミュニティメンバ種別マスタ
CREATE TABLE community_member_type_master (
	community_member_type_code CHAR(2) NOT NULL,  -- コミュニティ種別コード (P)
	community_member_type_name TEXT NOT NULL,     -- コミュニティ種別名
	CONSTRAINT community_member_type_master_pk PRIMARY KEY (community_member_type_code)  -- 31文字制限
);

COPY community_member_type_master FROM stdin;
10	管理者
20	メンバ
\.

-- カテゴリグループマスタ
CREATE TABLE category_group_master (
	category_group_code CHAR(4) NOT NULL,  -- カテゴリグループコード (P)
	category_group_name TEXT NOT NULL,     -- カテゴリグループ名
	CONSTRAINT category_group_master_pkey PRIMARY KEY (category_group_code)
);

-- カテゴリマスタ
CREATE TABLE category_master (
	category_code CHAR(4) NOT NULL,        -- カテゴリコード (P)
	category_name TEXT NOT NULL,           -- カテゴリ名
	category_group_code CHAR(4) NOT NULL,  -- カテゴリグループコード (F)
	CONSTRAINT category_master_pkey PRIMARY KEY (category_code),
	CONSTRAINT category_master_fkey1 FOREIGN KEY (category_group_code) REFERENCES category_group_master (category_group_code)
);

-- コンテンツ種別マスタ
CREATE TABLE contents_type_master (
	contents_type_code CHAR(2) NOT NULL,   -- コンテンツ種別コード (P)
	contents_type_name TEXT NOT NULL,      -- コンテンツ種別名
	CONSTRAINT contents_type_master_pkey PRIMARY KEY (contents_type_code)
);

COPY contents_type_master FROM stdin;
00	全体
01	氏名
02	メールアドレス
03	所属
04	専攻
05	出身
06	生年月日
07	プロフィール
08	プロフィール_ログインユーザ
09	プロフィール_フレンド
11	マイフレンズ
21	ダイアリー
31	フォルダ
32	フォルダ.フォルダ
33	ファイル
41	電子掲示板
42	電子掲示板.スレッド
43	電子掲示板.スレッド_非公開コミュニティ
51	メール言語
52	ラストログイン
53	マイページデザイン
61	コミュニティMLアドレス
62	コミュニティMLステータス
63	外部RSS.URL
64	外部RSS.投稿者
65	外部RSS.ML通知
66	外部RSS.パブリックリリース期間
\.

-- CREATE INDEX contents_type_master_index1 ON contents_type_master (contents_type_name);

-- 公開レベルマスタ
CREATE TABLE open_level_master (
	open_level_code CHAR(2) NOT NULL,                -- 公開レベルコード (P)
	open_level_name TEXT NOT NULL,                   -- 公開レベル名
	open_for_public BOOLEAN NOT NULL,                -- 一般ユーザに公開
	open_for_user BOOLEAN NOT NULL,                  -- ログインユーザに公開
	open_for_member BOOLEAN NOT NULL,                -- メンバに公開
	open_for_administrator BOOLEAN NOT NULL,         -- 管理者(本人やコミュニティ管理者)に公開
	open_for_system_administrator BOOLEAN NOT NULL,  -- システム管理者に公開
	CONSTRAINT open_level_master_pkey PRIMARY KEY (open_level_code)
);

COPY open_level_master FROM stdin;
01	一般公開	t	t	t	t	t
02	ログインユーザに公開	f	t	t	t	t
03	非公開	f	f	f	t	t
04	非公開 (メンバのみ)	f	f	t	t	t
05	友人に公開	f	f	t	t	t
06	パブリックリリース	t	t	t	t	t
\.

-- 公開レベルリスト (コミュニティ種別が保持する公開レベルのリスト)
CREATE TABLE open_level_list (
	community_type_code CHAR(2) NOT NULL,     -- コミュニティ種別コード (P) (F)
	contents_type_code CHAR(2) NOT NULL,      -- コンテンツ種別コード (P) (F)
	open_level_code CHAR(2) NOT NULL,         -- 公開レベルコード (P) (F)
	display_order INTEGER NOT NULL,           -- 表示順序
	is_default BOOLEAN NOT NULL DEFAULT 'f',  -- デフォルト値かどうか
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

-- 待機種別マスタ
CREATE TABLE waiting_type_master (
	waiting_type_code CHAR(2) NOT NULL,  -- 待機種別コード (P)
	waiting_type_name TEXT NOT NULL,     -- 待機種別名
	CONSTRAINT waiting_type_master_pkey PRIMARY KEY (waiting_type_code)
);

COPY waiting_type_master FROM stdin;
10	マイフレンズ追加
20	コミュニティ参加
30	コミュニティ招待
40	親コミュニティ追加
50	サブコミュニティ追加
\.

-- 待機状態マスタ
CREATE TABLE waiting_status_master (
	waiting_status_code CHAR(2) NOT NULL,  -- 待機状態コード (P)
	waiting_status_name TEXT NOT NULL,     -- 待機状態名
	CONSTRAINT waiting_status_master_pkey PRIMARY KEY (waiting_status_code)
);

COPY waiting_status_master FROM stdin;
10	承認待ち
20	承認済み
30	承認拒否
\.

-- ファイル種類マスタ
CREATE TABLE file_category_master (
	file_category_code CHAR(4) NOT NULL,  -- ファイル種類コード (P)
	file_category_name TEXT NOT NULL,     -- ファイル種類名
	CONSTRAINT file_category_master_pkey PRIMARY KEY (file_category_code)
);

COPY file_category_master FROM stdin;
0000	指定なし
0001	論文
0002	プレゼン
0003	画像
0004	動画
\.

-- ファイルコンテンツ種別マスタ
CREATE TABLE file_contents_type_master (
	file_contents_type_code CHAR(4) NOT NULL,  -- ファイルコンテンツ種別コード (P)
	file_contents_type_name TEXT NOT NULL,     -- ファイルコンテンツ種別名
	CONSTRAINT file_contents_type_master_pkey PRIMARY KEY (file_contents_type_code)
);

COPY file_contents_type_master FROM stdin;
0001	著者
0002	表題
0003	掲載誌・学会
0004	VolNo.等
0005	ページfrom
0006	ページto
0007	発表・掲載日
0008	言語
0009	開催地
0010	出版社
0011	論文種別
0012	備考
\.

-- ファイルコンテンツ種別リスト
CREATE TABLE file_contents_type_list (
	file_category_code CHAR(4) NOT NULL,       -- ファイル種類コード (P) (F)
	file_contents_type_code CHAR(4) NOT NULL,  -- ファイルコンテンツ種別コード (P) (F)
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

-- ファイル履歴操作マスタ
CREATE TABLE file_history_operation_master (
	file_history_operation_code CHAR(4) NOT NULL,  -- ファイル履歴操作コード (P)
	file_history_operation_name TEXT NOT NULL,  -- ファイル履歴操作名
	CONSTRAINT file_history_ope_master_pkey PRIMARY KEY (file_history_operation_code)  -- 31文字制限: operation → ope と短縮
);

COPY file_history_operation_master FROM stdin;
0101	作成
0201	更新
0301	復活
\.


--------------------------------
-- コミュニティ
--------------------------------

-- コミュニティ
CREATE TABLE community (
	community_id BIGINT NOT NULL,                          -- コミュニティID (P)
	community_name TEXT,                                   -- コミュニティ名
	community_type_code CHAR(2) NOT NULL,                  -- コミュニティ種別コード (F)
	category_code CHAR(4),                                 -- カテゴリコード (F)
	admission_flag BOOLEAN NOT NULL DEFAULT 'f',           -- 自由参加フラグ
	register_date TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP,  -- コミュニティ登録日時
	delete_flag BOOLEAN NOT NULL DEFAULT 'f',              -- 削除フラグ
	CONSTRAINT community_pkey PRIMARY KEY (community_id),
	CONSTRAINT community_fkey1 FOREIGN KEY (community_type_code) REFERENCES community_type_master (community_type_code),
	CONSTRAINT community_fkey2 FOREIGN KEY (category_code) REFERENCES category_master (category_code)

);
CREATE SEQUENCE community_id_seq;

-- サブコミュニティ
CREATE TABLE sub_community (
	community_id BIGINT NOT NULL,      -- 親コミュニティID (P) (F)
	sub_community_id BIGINT NOT NULL,  -- サブコミュニティID (P) (F)
	CONSTRAINT sub_community_pkey PRIMARY KEY (community_id, sub_community_id),
	CONSTRAINT sub_community_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT sub_community_fkey2 FOREIGN KEY (sub_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- コミュニティメンバ
CREATE TABLE community_member (
	community_id BIGINT NOT NULL,        -- コミュニティID (P) (F)
	user_community_id BIGINT NOT NULL,   -- メンバとなるコミュニティID(マイページ) (P) (F)
	community_member_type_code CHAR(2),  -- コミュニティメンバ種別コード(NULL許可) (P)
	CONSTRAINT community_member_pkey PRIMARY KEY (community_id, user_community_id),
	CONSTRAINT community_member_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT community_member_fkey2 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT community_member_fkey3 FOREIGN KEY (community_member_type_code) REFERENCES community_member_type_master (community_member_type_code)
);

-- (コミュニティの)コンテンツ
CREATE TABLE contents (
	community_id BIGINT NOT NULL,         -- コミュニティID (P) (F)
	contents_type_code CHAR(2) NOT NULL,  -- コンテンツ種別コード (P) (F)
	contents_value TEXT,                  -- コンテンツ値
	open_level_code CHAR(2) NOT NULL,     -- 公開レベルコード (P) (F)
	CONSTRAINT contents_pkey PRIMARY KEY (community_id, contents_type_code, open_level_code),
	CONSTRAINT contents_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT contents_fkey2 FOREIGN KEY (contents_type_code) REFERENCES contents_type_master (contents_type_code),
	CONSTRAINT contents_fkey3 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);

-- CREATE INDEX contents_index1 ON contents (community_id);
-- CREATE INDEX contents_index2 ON contents (contents_type_code);
-- CREATE INDEX contents_index3 ON contents (open_level_code);

-- コンテンツ 信頼済みコミュニティ
CREATE TABLE contents_trusted_community (
	community_id BIGINT NOT NULL,          -- コミュニティID (P) (F)
	contents_type_code CHAR(2) NOT NULL,   -- コンテンツ種別コード (P) (F)
	open_level_code CHAR(2) NOT NULL,     -- 公開レベルコード (P) (F)
	trusted_community_id BIGINT NOT NULL,  -- 信頼済みコミュニティID (P) (F)
	CONSTRAINT contents_trusted_community_pkey PRIMARY KEY (community_id, contents_type_code, open_level_code, trusted_community_id),
	CONSTRAINT contents_trusted_community_fk1 FOREIGN KEY (community_id, contents_type_code, open_level_code) REFERENCES contents (community_id, contents_type_code, open_level_code) ON DELETE CASCADE,  -- 31文字制限
	CONSTRAINT contents_trusted_community_fk2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE  -- 31文字制限
);

-- 参加 信頼済みコミュニティ
CREATE TABLE join_trusted_community (
	community_id BIGINT NOT NULL,          -- コミュニティID (P) (F)
	trusted_community_id BIGINT NOT NULL,  -- 信頼済みコミュニティID (P) (F)
	CONSTRAINT join_trusted_community_pkey PRIMARY KEY (community_id, trusted_community_id),
	CONSTRAINT join_trusted_community_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT join_trusted_community_fkey2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- 待機 (承認待機, 招待待機等)
CREATE TABLE waiting (
	waiting_id BIGINT NOT NULL,                                     -- 待機ID (P)
	community_id BIGINT NOT NULL,                                   -- コミュニティID (F)
	waiting_community_id BIGINT NOT NULL,                           -- 待機コミュニティID
	waiting_type_code CHAR(2) NOT NULL,                             -- 待機種別コード (F)
	waiting_status_code CHAR(2) NOT NULL,                           -- 待機状態コード (F)
	message TEXT,                                                   -- メッセージ
	reply_message TEXT,                                             -- 返信メッセージ
	entry_user_community_id BIGINT NOT NULL,                        -- 登録ユーザコミュニティID
	entry_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,     -- 登録日
	complete_user_community_id BIGINT,                              -- 完了ユーザコミュニティID
	complete_date TIMESTAMP(0),                                     -- 完了日
	CONSTRAINT waiting_community_member_pkey PRIMARY KEY (waiting_id),
	CONSTRAINT waiting_community_member_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT waiting_community_member_fkey2 FOREIGN KEY (waiting_type_code) REFERENCES waiting_type_master (waiting_type_code),
	CONSTRAINT waiting_community_member_fkey3 FOREIGN KEY (waiting_status_code) REFERENCES waiting_status_master (waiting_status_code)
);

CREATE SEQUENCE waiting_id_seq;


--------------------------------
-- ダイアリー
--------------------------------

-- ダイアリー
CREATE TABLE diary (
	diary_id BIGINT NOT NULL,                                   -- ダイアリーID (P)
	community_id BIGINT NOT NULL,                               -- (ユーザ)コミュニティID (F)
	subject TEXT,                                               -- 件名
	body TEXT NOT NULL,                                         -- 内容
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 投稿日時
	open_level_code CHAR(2) NOT NULL,                           -- 公開範囲 (F)
	diary_delete_flag BOOLEAN NOT NULL DEFAULT 'f',             -- 削除フラグ
	CONSTRAINT diary_pkey PRIMARY KEY (diary_id),
	CONSTRAINT diary_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT diary_fkey2 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);
CREATE SEQUENCE diary_id_seq;

-- ダイアリーコメント
CREATE TABLE diary_comment (
	diary_comment_id BIGINT NOT NULL,                           -- ダイアリーコメントID (P)
	diary_id BIGINT NOT NULL,                                   -- ダイアリーID(親記事) (F)
	user_community_id BIGINT NOT NULL,                          -- 投稿者のコミュニティID(マイページ)
	body TEXT NOT NULL,                                         -- 内容
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 投稿日時
	diary_comment_delete_flag BOOLEAN NOT NULL DEFAULT 'f',     -- 削除フラグ
	CONSTRAINT diary_comment_pkey PRIMARY KEY (diary_comment_id),
	CONSTRAINT diary_comment_fkey1 FOREIGN KEY (diary_id) REFERENCES diary (diary_id) ON DELETE CASCADE
);
CREATE SEQUENCE diary_comment_id_seq;

-- ダイアリー 信頼済みコミュニティ
CREATE TABLE diary_trusted_community (
	diary_id BIGINT NOT NULL,              -- ダイアリーID (P) (F)
	trusted_community_id BIGINT NOT NULL,  -- 信頼済みコミュニティID (P) (F)
	CONSTRAINT diary_trusted_community_pkey PRIMARY KEY (diary_id, trusted_community_id),
	CONSTRAINT diary_trusted_community_fkey1 FOREIGN KEY (diary_id) REFERENCES diary (diary_id) ON DELETE CASCADE,
	CONSTRAINT diary_trusted_community_fkey2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- ダイアリーアクセス履歴
CREATE TABLE diary_access_history (
	user_community_id BIGINT NOT NULL,  -- アクセス者のユーザコミュニティID
	diary_id BIGINT NOT NULL,           -- アクセス対象のダイアリーID
	access_date TIMESTAMP(0) NOT NULL,  -- アクセス日時
	CONSTRAINT diary_access_history_pkey PRIMARY KEY (user_community_id, diary_id),
	CONSTRAINT diary_access_history_fkey1 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT diary_access_history_fkey2 FOREIGN KEY (diary_id) REFERENCES diary (diary_id) ON DELETE CASCADE
);


--------------------------------
-- フォルダ
--------------------------------

-- ファイル情報
CREATE TABLE file_info (
	file_id BIGINT NOT NULL,                                      -- ファイルID (P)
	owner_community_id BIGINT NOT NULL,                           -- 所有者コミュニティID
	display_file_name TEXT NOT NULL,                              -- 表示ファイル名
	server_file_name TEXT NOT NULL,                               -- サーバファイル名
	thumbnail_server_file_name TEXT,                              -- サムネイルサーバファイル名
	rss_server_file_name TEXT,                                    -- RSSサーバファイル名
	mime_type TEXT,                                               -- MimeType
	file_size INTEGER,                                            -- ファイルサイズ
	comment TEXT,                                                 -- コメント
	entry_user_community_id BIGINT,                               -- 登録ユーザコミュニティID
	entry_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- 登録日
	update_user_community_id BIGINT,                              -- 更新ユーザコミュニティID
	update_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 更新日
	CONSTRAINT file_info_pkey PRIMARY KEY (file_id),
	CONSTRAINT file_info_fkey1 FOREIGN KEY (owner_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);
CREATE SEQUENCE file_id_seq;

-- フォルダ
CREATE TABLE folder (
	folder_id BIGINT NOT NULL,                                    -- フォルダID (P)
	community_id BIGINT NOT NULL,                                 -- コミュニティID (F)
	folder_name TEXT NOT NULL,                                    -- フォルダ名
	comment TEXT,                                                 -- コメント
	parent_folder_id BIGINT,                                      -- 親フォルダID (F)
	entry_user_community_id BIGINT,                               -- 登録ユーザコミュニティID
	entry_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- 登録日
	update_user_community_id BIGINT,                              -- 更新ユーザコミュニティID
	update_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 更新日
	open_level_code CHAR(2),                                      -- 公開レベルコード (F)
	CONSTRAINT folder_pkey PRIMARY KEY (folder_id),
	CONSTRAINT folder_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT folder_fkey2 FOREIGN KEY (parent_folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE,
	CONSTRAINT folder_fkey3 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);
CREATE SEQUENCE folder_id_seq;

-- フォルダファイル
CREATE TABLE folder_file (
	folder_id BIGINT NOT NULL,            -- フォルダID (P) (F)
	file_id BIGINT NOT NULL,              -- ファイルID (P) (F)
	CONSTRAINT folder_file_pkey PRIMARY KEY (folder_id, file_id),
	CONSTRAINT folder_file_fkey1 FOREIGN KEY (folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE,
	CONSTRAINT folder_file_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);

-- フォルダ 信頼済みコミュニティ
CREATE TABLE folder_trusted_community (
	folder_id BIGINT NOT NULL,             -- フォルダID (P) (F)
	trusted_community_id BIGINT NOT NULL,  -- 信頼済みコミュニティID (P) (F)
	PRIMARY KEY (folder_id, trusted_community_id),
	CONSTRAINT folder_trusted_community_fkey1 FOREIGN KEY (folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE,
	CONSTRAINT folder_trusted_community_fkey2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- プットコミュニティ
CREATE TABLE put_community (
	folder_id BIGINT NOT NULL,                -- フォルダID (P) (F)
	put_community_id BIGINT NOT NULL,         -- プット先コミュニティID (P) (F)
	put_community_folder_id BIGINT NOT NULL,  -- プット先コミュニティフォルダID (P) (F)
	CONSTRAINT put_community_pkey PRIMARY KEY (folder_id, put_community_id, put_community_folder_id),
	CONSTRAINT put_community_fkey1 FOREIGN KEY (folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE,
	CONSTRAINT put_community_fkey2 FOREIGN KEY (put_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT put_community_fkey3 FOREIGN KEY (put_community_folder_id) REFERENCES folder (folder_id) ON DELETE CASCADE
);

-- ファイル詳細情報
CREATE TABLE file_detail_info (
	file_id BIGINT NOT NULL,                                      -- ファイルID (P) (F)
	file_category_code CHAR(4) NOT NULL,                          -- ファイルカテゴリコード (F)
	update_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 更新日
	CONSTRAINT file_detail_info_pkey PRIMARY KEY (file_id),
	CONSTRAINT file_detail_info_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE,
	CONSTRAINT file_detail_info_fkey3 FOREIGN KEY (file_category_code) REFERENCES file_category_master (file_category_code)
);

-- ファイルコンテンツ(詳細情報)
CREATE TABLE file_contents (
	file_id BIGINT NOT NULL,                   -- ファイルID (P) (F)
	file_contents_type_code CHAR(4) NOT NULL,  -- ファイルコンテンツ種別コード (P) (F)
	file_contents_value TEXT,                  -- ファイルコンテンツ値
	CONSTRAINT file_contents_pkey PRIMARY KEY (file_id, file_contents_type_code),
	CONSTRAINT file_contents_fkey1 FOREIGN KEY (file_id) REFERENCES file_detail_info (file_id) ON DELETE CASCADE,
	CONSTRAINT file_contents_fkey2 FOREIGN KEY (file_contents_type_code) REFERENCES file_contents_type_master (file_contents_type_code)
);

-- ファイル履歴
CREATE TABLE file_history (
	file_history_id BIGINT NOT NULL,               -- ファイル履歴ID (P)
	file_id BIGINT NOT NULL,                       -- ファイルID (F)
	display_file_name TEXT NOT NULL,               -- ファイル表示名
	server_file_name TEXT NOT NULL,                -- サーバファイル名
	thumbnail_server_file_name TEXT,               -- サムネイルサーバファイル名
	mime_type TEXT,                                -- Mime Type
	file_size INTEGER,                             -- ファイルサイズ
	update_date TIMESTAMP(0) NOT NULL,              -- 登録日
	update_user_community_id BIGINT,                -- 登録ユーザコミュニティID
	file_history_operation_code CHAR(4) NOT NULL,  -- ファイル履歴操作コード (F)
	CONSTRAINT file_history_pkey PRIMARY KEY (file_history_id),
	CONSTRAINT file_history_fkey1 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE,
	CONSTRAINT file_history_fkey2 FOREIGN KEY (file_history_operation_code) REFERENCES file_history_operation_master (file_history_operation_code)
);

CREATE SEQUENCE file_history_id_seq;

-- ファイル履歴コメント
CREATE TABLE file_history_comment (
	file_history_comment_id BIGINT NOT NULL,                    -- ファイル履歴コメントID (P)
	file_history_id BIGINT NOT NULL,                            -- ファイル履歴ID (F)
	user_community_id BIGINT NOT NULL,                          -- 投稿者のユーザコミュニティID
	comment TEXT,                                               -- コメント
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 投稿日
	CONSTRAINT file_history_comment_pkey PRIMARY KEY (file_history_comment_id),
	CONSTRAINT file_history_comment_fkey1 FOREIGN KEY (file_history_id) REFERENCES file_history (file_history_id) ON DELETE CASCADE
);

CREATE SEQUENCE file_history_comment_id_seq;

-- ファイルアクセス履歴
CREATE TABLE file_access_history (
	user_community_id BIGINT NOT NULL,  -- アクセス者のユーザコミュニティID (P)(F)
	file_id BIGINT NOT NULL,            -- アクセス対象のfile_id (P)(F)
	access_date TIMESTAMP(0) NOT NULL,  -- アクセス日時
	CONSTRAINT file_access_history_pkey PRIMARY KEY (user_community_id, file_id),
	CONSTRAINT file_access_history_fkey1 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT file_access_history_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);

-- ファイル公開情報テーブル
CREATE TABLE file_public_access (
	file_id BIGINT NOT NULL,                               				-- ファイルID
	folder_id BIGINT NOT NULL,                             				-- フォルダーID
	community_id BIGINT NOT NULL,                          				-- コミュニティID
	access_code TEXT,                  									-- アクセスコード
	all_access_count BIGINT NOT NULL DEFAULT 0,            				-- 総アクセス数
	access_count BIGINT NOT NULL DEFAULT 0,            					-- アクセス数
	access_start_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP, 	-- アクセス数開始日
	update_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,        -- 更新日時
	CONSTRAINT file_public_access_pkey PRIMARY KEY (file_id),
	CONSTRAINT file_public_access_fkey1 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);

--------------------------------
-- コミュニティ
--------------------------------

-- 掲示板
CREATE TABLE bbs (
	bbs_id BIGINT NOT NULL,                                     -- 投稿ID (P)
	community_id BIGINT NOT NULL,                               -- コミュニティID (F)
	user_community_id BIGINT NOT NULL,                          -- 投稿者のコミュニティID(マイページ)
	subject TEXT,                                               -- 件名
	body TEXT NOT NULL,                                         -- 内容
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 投稿日時
	open_level_code CHAR(2) NOT NULL,                           -- 公開レベルコード
	expire_date TIMESTAMP(0),                                   -- 掲載終了日
	bbs_delete_flag BOOLEAN NOT NULL DEFAULT 'f',               -- 削除フラグ
	ml_send_flag BOOLEAN NOT NULL DEFAULT 'f',                  -- ML送信フラグ
	CONSTRAINT bbs_pkey PRIMARY KEY (bbs_id),
	CONSTRAINT bbs_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT bbs_fkey2 FOREIGN KEY (open_level_code) REFERENCES open_level_master (open_level_code)
);
CREATE SEQUENCE bbs_id_seq;

-- 掲示板返信
CREATE TABLE bbs_res (
	bbs_res_id BIGINT NOT NULL,                                 -- 返信投稿ID (P)
	bbs_id BIGINT NOT NULL,                                     -- 投稿ID(親記事) (F)
	user_community_id BIGINT NOT NULL,                          -- 投稿者のコミュニティID(マイページ)
	subject TEXT,                                               -- 件名
	body TEXT NOT NULL,                                         -- 内容
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 投稿日時
	bbs_res_delete_flag BOOLEAN NOT NULL DEFAULT 'f',           -- 削除フラグ
	CONSTRAINT bbs_res_pkey PRIMARY KEY (bbs_res_id),
	CONSTRAINT bbs_res_fkey1 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE
);
CREATE SEQUENCE bbs_res_id_seq;

-- 掲示板信頼済みコミュニティ
CREATE TABLE bbs_trusted_community (
	bbs_id BIGINT NOT NULL,                -- 掲示板投稿ID
	trusted_community_id BIGINT NOT NULL,  -- 信頼済みコミュニティID
	CONSTRAINT bbs_trusted_community_pkey PRIMARY KEY (bbs_id, trusted_community_id),
	CONSTRAINT bbs_trusted_community_fkey1 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE,
	CONSTRAINT bbs_trusted_community_fkey2 FOREIGN KEY (trusted_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);

-- 掲示板アクセス履歴
CREATE TABLE bbs_access_history (
	user_community_id BIGINT NOT NULL,  -- アクセス者のユーザコミュニティID
	bbs_id BIGINT NOT NULL,             -- アクセス対象のbbs_id
	access_date TIMESTAMP(0) NOT NULL,  -- アクセス日時
	CONSTRAINT bbs_access_history_pkey PRIMARY KEY (user_community_id, bbs_id),
	CONSTRAINT bbs_access_history_fkey1 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT bbs_access_history_fkey2 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE
);

-- (掲示板)外部RSS
CREATE TABLE external_rss (
	bbs_id BIGINT NOT NULL,               -- bbs_id (P) (F)
	rss_url TEXT NOT NULL,                -- RSS取込元URL
	rss_channel_title TEXT NOT NULL,      -- RSSチャンネルタイトル
	rss_item_title TEXT NOT NULL,         -- RSSアイテムtitle
	rss_item_content TEXT NOT NULL,       -- RSSアイテムcontent
	rss_item_date TIMESTAMP(0),           -- RSSアイテムdate
	rss_item_link TEXT,                   -- RSSアイテムlink
	CONSTRAINT external_rss_pkey PRIMARY KEY (bbs_id),
	CONSTRAINT external_rss_fkey1 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE
);

-- スケジュール
CREATE TABLE schedule (
	schedule_id BIGINT NOT NULL,                      -- スケジュールID (P)
	community_id BIGINT NOT NULL,                     -- コミュニティID (F)
	user_community_id BIGINT NOT NULL,                -- 幹事のコミュニティID(マイページ)
	schedule_name TEXT NOT NULL,                      -- スケジュール名
	schedule_place TEXT NOT NULL,                     -- 場所
	schedule_detail TEXT,                             -- 詳細情報
	schedule_closing_datetime TIMESTAMP(0) NOT NULL,  -- 投稿日時
	schedule_target_kind VARCHAR(4) NOT NULL DEFAULT 'ALL',    -- 対象種別('ALL'/'FREE')
	decide_adjustment_date_id BIGINT NOT NULL DEFAULT 0,       -- スケジュール決定日時ID
	entry_datetime TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 作成日時
	update_datetime TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP, -- 更新日時

	CONSTRAINT schedule_pkey PRIMARY KEY (schedule_id),
	CONSTRAINT schedule_fkey1 FOREIGN KEY (community_id) 
			REFERENCES community (community_id) ON DELETE CASCADE
);
CREATE SEQUENCE schedule_id_seq;

-- スケジュール候補日時
CREATE TABLE schedule_adjustment_dates (
	schedule_id BIGINT NOT NULL,                    -- スケジュールID (P)(F)
	adjustment_date_id BIGINT NOT NULL,             -- スケジュール候補日時ID (P)
	adjustment_date_string TEXT NOT NULL,           -- スケジュール候補日時
	adjustment_date_delete_flag BOOLEAN NOT NULL DEFAULT 'f', 
													-- 削除フラグ(削除時TRUE)
	CONSTRAINT schedule_adjustment_dates_pkey PRIMARY KEY (schedule_id,adjustment_date_id),
	CONSTRAINT schedule_adjustment_dates_fkey1 FOREIGN KEY (schedule_id) 
			REFERENCES schedule (schedule_id) ON DELETE CASCADE
);
CREATE SEQUENCE adjustment_date_id_seq;

-- スケジュール回答選択肢
CREATE TABLE schedule_answer_selection (
	schedule_id BIGINT NOT NULL,                    -- スケジュールID (P)(F)
	answer_no BIGINT NOT NULL,                      -- スケジュール回答番号 (P)
	answer_char VARCHAR(1),        			        -- スケジュール回答記号
	answer_score SMALLINT,  		                -- スケジュール回答スコア
	answer_detail TEXT,                             -- スケジュール回答説明
	answer_default BOOLEAN NOT NULL DEFAULT 'f',    -- スケジュール回答初期値フラグ
	CONSTRAINT schedule_answer_selection_pkey PRIMARY KEY (schedule_id,answer_no),
	CONSTRAINT schedule_answer_selection_fkey1 FOREIGN KEY (schedule_id) 
			REFERENCES schedule (schedule_id) ON DELETE CASCADE
);

-- スケジュール参加
CREATE TABLE schedule_participant (
	schedule_id BIGINT NOT NULL,                    -- スケジュールID (P)(F)
	user_community_id BIGINT NOT NULL,              -- 回答者のコミュニティID(マイページ)(P)
	participant_comment TEXT,                       -- 参加コメント
	participant_delete_flag BOOLEAN NOT NULL DEFAULT 'f', -- 削除フラグ(参加とりやめ時=TRUE)
	CONSTRAINT schedule_participant_pkey PRIMARY KEY (schedule_id,user_community_id),
	CONSTRAINT schedule_participant_fkey1 FOREIGN KEY (schedule_id) 
			REFERENCES schedule (schedule_id) ON DELETE CASCADE
);

-- スケジュール回答
CREATE TABLE schedule_answer (
	schedule_id BIGINT NOT NULL,                    -- スケジュールID (P)(F)
	user_community_id BIGINT NOT NULL,              -- 回答者のコミュニティID(マイページ)(P)
	adjustment_date_id BIGINT NOT NULL,             -- スケジュール候補日時ID (P)(F)
	answer_no BIGINT NOT NULL,                      -- スケジュール回答番号 (P)
	CONSTRAINT schedule_answer_pkey PRIMARY KEY (
			schedule_id,user_community_id,adjustment_date_id,answer_no),
	CONSTRAINT schedule_answer_fkey1 FOREIGN KEY (schedule_id) 
			REFERENCES schedule (schedule_id) ON DELETE CASCADE,
	CONSTRAINT schedule_answer_fkey2 FOREIGN KEY (schedule_id,adjustment_date_id)
			REFERENCES schedule_adjustment_dates (schedule_id,adjustment_date_id) 
			ON DELETE CASCADE
);


--------------------------------
-- 各アップロードファイル
--------------------------------

-- コミュニティ画像ファイル
CREATE TABLE community_image_file (
	community_id BIGINT NOT NULL,  -- コミュニティID (P) (F)
	file_id BIGINT NOT NULL,       -- ファイルID (P) (F)
	file_id_ol01 bigint,           -- 公開レベル01用ファイルID
	file_id_ol02 bigint,           -- 公開レベル02用ファイルID
	file_id_ol05 bigint,           -- 公開レベル05用ファイルID
	CONSTRAINT community_image_file_pkey PRIMARY KEY (community_id, file_id),
	CONSTRAINT community_image_file_fkey1 FOREIGN KEY (community_id) REFERENCES community (community_id) ON DELETE CASCADE,
	CONSTRAINT community_image_file_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE,
	CONSTRAINT community_image_file_fkey3 FOREIGN KEY (file_id_ol01) REFERENCES file_info (file_id) ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT community_image_file_fkey4 FOREIGN KEY (file_id_ol02) REFERENCES file_info (file_id) ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT community_image_file_fkey5 FOREIGN KEY (file_id_ol05) REFERENCES file_info (file_id) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- ダイアリーファイル
CREATE TABLE diary_file (
	diary_id BIGINT NOT NULL,  -- ダイアリーID (P) (F)
	file_id BIGINT NOT NULL,   -- ファイルID (P) (F)
	CONSTRAINT diary_file_pkey PRIMARY KEY (diary_id, file_id),
	CONSTRAINT diary_file_fkey1 FOREIGN KEY (diary_id) REFERENCES diary (diary_id) ON DELETE CASCADE,
	CONSTRAINT diary_file_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);

-- 掲示板ファイル
CREATE TABLE bbs_file (
	bbs_id BIGINT NOT NULL,   -- 掲示板ID (P) (F)
	file_id BIGINT NOT NULL,  -- ファイルID (P) (F)
	CONSTRAINT bbs_file_pkey PRIMARY KEY (bbs_id, file_id),
	CONSTRAINT bbs_file_fkey1 FOREIGN KEY (bbs_id) REFERENCES bbs (bbs_id) ON DELETE CASCADE,
	CONSTRAINT bbs_file_fkey2 FOREIGN KEY (file_id) REFERENCES file_info (file_id) ON DELETE CASCADE
);


--------------------------------
-- ユーザ情報
--------------------------------

-- ユーザ情報
CREATE TABLE user_info (
	user_id TEXT NOT NULL,                            -- ユーザID (P)
	user_community_id BIGINT NOT NULL,                -- コミュニティID(マイページ) (F)
	administrator_flag BOOLEAN NOT NULL DEFAULT 'f',  -- システム管理者フラグ
	CONSTRAINT user_info_pkey PRIMARY KEY (user_id, user_community_id),
	CONSTRAINT user_info_fkey1 FOREIGN KEY (user_community_id) REFERENCES community (community_id) ON DELETE CASCADE
);


--------------------------------
-- ログ管理
--------------------------------

-- 操作マスタ
CREATE TABLE operation_master (
	operation_code CHAR(4) NOT NULL,  -- 操作コード (P)
	operation_name TEXT NOT NULL,     -- 操作名
	CONSTRAINT operation_master_en_pkey PRIMARY KEY (operation_code)
);

-- #### 旧 日本語データ #### --
-- COPY operation_master FROM stdin;
-- 0101	ログイン
-- 0201	ユーザ新規登録
-- 0202	LDAPユーザ新規登録
-- 0203	ユーザ情報変更
-- 0204	ユーザ削除
-- 0301	システム設定変更
-- \.

-- #### 英語版 ####
COPY operation_master FROM stdin;
0101	Login
0201	New User Registration
0202	New LDAP User Registration
0203	Change User Information
0204	Remove User
0301	Change System Settings
\.


-- ログ
CREATE TABLE log (
	log_id BIGINT NOT NULL,                                    -- ログID (P)
	log_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- ログ日付
	user_id TEXT NOT NULL,                                     -- ユーザID
	user_name TEXT NOT NULL,                                   -- 氏名
	user_community_id BIGINT NOT NULL,                         -- ユーザコミュニティID
	community_name TEXT,                                       -- ニックネーム
	administrator_flag BOOLEAN NOT NULL,                       -- システム管理者フラグ
	operation_code CHAR(4) NOT NULL,                           -- 操作コード
	operation_result BOOLEAN NOT NULL,                         -- 操作結果 (t=成功, f=失敗)
	message TEXT,                                              -- メッセージ(操作内容)
	CONSTRAINT log_pkey PRIMARY KEY (log_id),
	CONSTRAINT log_fkey1 FOREIGN KEY (operation_code) REFERENCES operation_master (operation_code)
);

CREATE SEQUENCE log_id_seq;

-- 足跡
CREATE TABLE footprint (
	community_id BIGINT NOT NULL,                               -- 足跡を付けられたユーザ
	visitor_community_id BIGINT NOT NULL,                       -- 足跡を付けたユーザ
	contents_type_code CHAR(2) NOT NULL,                        -- (21=ダイアリー, 33=ファイル[新設]) 
	contents_title TEXT NOT NULL,                               -- リンク名
	contents_link_url TEXT NOT NULL,                            -- リンクURL
	contents_date TIMESTAMP(0) NOT NULL,                        -- 足跡が付いた時点のコンテンツの日時
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 足跡日時
	CONSTRAINT footprint_fkey1 FOREIGN KEY (contents_type_code) REFERENCES contents_type_master (contents_type_code)
);

--------------------------------
-- システムからのお知らせ
--------------------------------

-- システムからのお知らせ
CREATE TABLE system_announce (
	system_announce_id BIGINT NOT NULL,                         -- システムアナウンスID (P)
	user_community_id BIGINT NOT NULL,                          -- 投稿を行ったユーザコミュニティID
	subject TEXT NOT NULL,                                      -- 件名
	body TEXT NOT NULL,                                         -- 本文
	post_date TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- 投稿日時
	expire_date DATE,                                           -- 掲載終了日
	system_announce_delete_flag BOOLEAN NOT NULL DEFAULT 'f',   -- 削除フラグ
	CONSTRAINT system_announce_pkey PRIMARY KEY (system_announce_id)
);

CREATE SEQUENCE system_announce_id_seq;


--------------------------------
-- システム設定
--------------------------------

-- システム設定グループ
CREATE TABLE system_config_group (
	system_config_group_code CHAR(2) NOT NULL,  -- グループコード (P)
	system_config_group_name TEXT NOT NULL,     -- グループ名
	display_order INTEGER NOT NULL,             -- 表示順序
	CONSTRAINT system_config_group_pkey PRIMARY KEY (system_config_group_code)
);

-- システム設定
CREATE TABLE system_config (
	system_config_group_code CHAR(2) NOT NULL,  -- グループコード
	keyword TEXT NOT NULL,                      -- キーワード (P)
	value TEXT NOT NULL,                        -- 値
	type TEXT NOT NULL,                         -- 型 (number or string)
	display_order INTEGER NOT NULL,             -- 表示順序
	CONSTRAINT system_config_pkey PRIMARY KEY (keyword),
	CONSTRAINT system_config_fkey1 FOREIGN KEY (system_config_group_code) REFERENCES system_config_group (system_config_group_code) ON DELETE CASCADE
);

COPY system_config_group FROM stdin;
01	システム	1
02	マイページ	2
03	コミュニティ	3
04	画像ファイル	4
05	ランキング	5
06	パブリックリリース	6
07	ログ	7
08	ユーザ情報	8
\.

COPY system_config FROM stdin;
01	SYSTEM_NAME	アカデミックコミュニティシステム	string	1
01	SYSTEM_OUTLINE	アカデミックコミュニティシステムです	string	2
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
-- ログアウト対応
--------------------------------

-- ログイン情報
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
-- メッセージ機能
--------------------------------

-- メッセージ
CREATE TABLE message
(
	message_id bigint NOT NULL,
	subject text NOT NULL,
	body text,
	post_date timestamp(0) without time zone NOT NULL DEFAULT now(),
	CONSTRAINT message_pkey PRIMARY KEY (message_id)
) ;
CREATE SEQUENCE message_id_seq;

-- メッセージ受信者
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

-- メッセージ送信者
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
-- 性能改善用インデックス
----------------------------------------------------------------
--
-- 時系列カラムのインデックス作成
--
CREATE INDEX diary_post_date ON diary (post_date);
CREATE INDEX diary_comment_post_date ON diary_comment (post_date);
CREATE INDEX bbs_post_date ON bbs (post_date);
CREATE INDEX bbs_res_post_date ON bbs_res (post_date);
CREATE INDEX file_info_entry_date ON file_info (entry_date);
CREATE INDEX file_info_update_date ON file_info (update_date);

----------------------------------------------------------------
-- ビュー
----------------------------------------------------------------
--
-- SQL Functionに変わるビューの作成
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
-- ユーザ定義関数
----------------------------------------------------------------

----------------------------------------------------
-- 関数: TIMESTAMPをYYYY/MM/DD(wday) H:MMに変換する
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
         WHEN wday = ''1'' THEN ''日''
         WHEN wday = ''2'' THEN ''月''
         WHEN wday = ''3'' THEN ''火''
         WHEN wday = ''4'' THEN ''水''
         WHEN wday = ''5'' THEN ''木''
         WHEN wday = ''6'' THEN ''金''
         WHEN wday = ''7'' THEN ''土''
       END AS jday
     FROM
       (SELECT to_char($1, ''D'') AS wday) AS dummy1
    ) AS dummy2
' LANGUAGE 'sql';

-- 2つの時刻を比較して最新の方を取得する
CREATE FUNCTION acs_get_last_timestamp(TIMESTAMP(0), TIMESTAMP(0))
RETURNS TIMESTAMP(0) AS '
  SELECT
    CASE
      WHEN $2 is null THEN $1
      WHEN $1 >= $2 THEN $1
      ELSE $2
    END AS ret_timestamp
' LANGUAGE 'sql';


-- ダイアリーIDを指定してコメントの件数を取得する
CREATE FUNCTION acs_get_diary_comment_num(BIGINT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM diary_comment
    WHERE diary_id = $1
' LANGUAGE 'sql';

-- ダイアリーIDを指定して最終投稿日時を取得する (親記事とコメントの投稿日時も比較する)
CREATE FUNCTION acs_get_diary_last_post_date(BIGINT)
RETURNS TIMESTAMP(0) AS '
  SELECT acs_get_last_timestamp(diary.post_date, diary_comment.post_date) as diary_last_post_date
    FROM diary LEFT OUTER JOIN diary_comment USING(diary_id)
    WHERE diary.diary_id = $1
    ORDER BY diary_last_post_date DESC
    LIMIT 1
' LANGUAGE 'sql';

-- ダイアリーIDを指定してダイアリーコメントの最終投稿日時を取得する (コメントが0件の場合はnull)
-- 31文字制限: comment → c と短縮
CREATE FUNCTION acs_get_diary_c_last_post_date(BIGINT)
RETURNS TIMESTAMP(0) AS '
  SELECT diary_comment.post_date
    FROM diary, diary_comment
    WHERE diary.diary_id = $1
      AND diary.diary_id = diary_comment.diary_id
    ORDER BY diary_comment.post_date DESC
    LIMIT 1
' LANGUAGE 'sql';

-- ユーザコミュニティIDとダイアリーIDを指定して未読かどうか判別する
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

-- ユーザコミュニティIDとダイアリーIDを指定してダイアリーコメントが未読かどうか判別する
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

-- ユーザコミュニティIDとファイルIDを指定してファイルが未読かどうか判別する
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


-- bbs_idを指定して返信の件数を取得する
CREATE FUNCTION acs_get_bbs_res_num(BIGINT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM bbs_res
    WHERE bbs_res.bbs_id = $1
' LANGUAGE 'sql';

-- bbs_idを指定して最終投稿日時を取得する (親記事と返信記事の投稿日時も比較する)
CREATE FUNCTION acs_get_bbs_last_post_date(BIGINT)
RETURNS TIMESTAMP(0) AS '
  SELECT acs_get_last_timestamp(bbs.post_date, bbs_res.post_date) as bbs_last_post_date
    FROM bbs LEFT OUTER JOIN bbs_res USING(bbs_id)
    WHERE bbs.bbs_id = $1
    ORDER BY bbs_last_post_date DESC
    LIMIT 1
' LANGUAGE 'sql';

-- ユーザコミュニティIDとダイアリーIDを指定してスレッドが未読かどうか判別する
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


-- ユーザのマイフレンズ数を取得する
CREATE FUNCTION acs_get_friends_num(BIGINT)
RETURNS BIGINT AS ' 
  SELECT count(*)
    FROM community, sub_community, community as FRIENDS_COMMUNITY, community_type_master, community_member,
         community as USER_COMMUNITY
    WHERE community.community_id = $1
      AND community.community_id = sub_community.community_id
      AND sub_community.sub_community_id = FRIENDS_COMMUNITY.community_id
      AND FRIENDS_COMMUNITY.community_type_code = community_type_master.community_type_code
      AND community_type_master.community_type_name = ''マイフレンズ''
      AND FRIENDS_COMMUNITY.community_id = community_member.community_id
      AND community_member.user_community_id = USER_COMMUNITY.community_id
      AND USER_COMMUNITY.delete_flag != ''t''
' LANGUAGE 'sql';

-- ユーザのマイコミュニティ数を取得する
CREATE FUNCTION acs_get_community_num(BIGINT)
RETURNS BIGINT AS ' 
  SELECT count(*)
    FROM community, community_type_master, community_member
    WHERE community.community_type_code = community_type_master.community_type_code
      AND community.community_id = community_member.community_id
      AND community_type_master.community_type_name = ''コミュニティ''
      AND community_member.user_community_id = $1
      AND community.delete_flag != ''t''
' LANGUAGE 'sql';

-- コミュニティのメンバ数を取得する
CREATE FUNCTION acs_get_community_member_num(BIGINT)
RETURNS BIGINT AS ' 
  SELECT count(*)
    FROM community_member, community as USER_COMMUNITY
    WHERE community_member.community_id = $1
      AND community_member.user_community_id = USER_COMMUNITY.community_id
      AND USER_COMMUNITY.delete_flag != ''t''
' LANGUAGE 'sql';

-- $1が$2コミュニティのメンバかどうか取得する (t/f)
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


-- ランキング --
-- ユーザコミュニティID($1)が投稿したdiaryテーブル数を集計する
CREATE FUNCTION acs_get_diary_score(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM diary
    WHERE diary.community_id = $1
      AND diary.diary_delete_flag != ''t''
      AND diary.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
' LANGUAGE 'sql';

-- ユーザコミュニティID($1)が投稿したdiary_commentテーブル数を集計する
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

-- ユーザコミュニティID($1)に投稿されたdiary_commentテーブル数を集計する
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


-- ユーザコミュニティID($1)が投稿したbbsテーブル数を集計する
CREATE FUNCTION acs_get_bbs_score_by_u_c_id(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM bbs
    WHERE bbs.community_id = $1
      AND bbs.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
      AND bbs.bbs_delete_flag != ''t''
' LANGUAGE 'sql';

-- ユーザコミュニティID($1)が投稿したbbs_resテーブル数を集計する
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

-- コミュニティID($1)に投稿されたbbsテーブル数を集計する
CREATE FUNCTION acs_get_bbs_score_by_c_id(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM bbs
    WHERE bbs.community_id = $1
      AND bbs.bbs_delete_flag != ''t''
      AND bbs.post_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
' LANGUAGE 'sql';

-- コミュニティID($1)に投稿されたbbs_resテーブル数を集計する
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

-- コミュニティID($1)のfile_infoテーブル数を集計する
CREATE FUNCTION acs_get_file_info_score(BIGINT, TEXT)
RETURNS BIGINT AS '
  SELECT count(*)
    FROM file_info
    WHERE file_info.owner_community_id = $1
      AND file_info.update_date::DATE >= (CURRENT_DATE - (''@ '' || $2 || '' days'')::INTERVAL)
' LANGUAGE 'sql';


-- システムアナウンス --
-- 掲載期限内かどうかを取得する
CREATE FUNCTION acs_is_expire_date(DATE)
RETURNS BOOLEAN AS '
  SELECT (CURRENT_DATE > $1)::BOOLEAN
' LANGUAGE 'sql';


-- コミット
COMMIT;


----------------------------------------------------------------
--**************************************************************
-- システム管理者情報
--**************************************************************
BEGIN;

-- システム管理者のユーザ情報
-- マイページ(ユーザコミュニティ)
INSERT INTO community (community_id, community_name, community_type_code) VALUES (1, 'システム管理者', '10');
-- ユーザ情報
INSERT INTO user_info (user_id, user_community_id, administrator_flag) VALUES ('admin', 1, 't');
-- 氏名
INSERT INTO contents (community_id, contents_type_code, contents_value, open_level_code) VALUES (1, '01', 'システム管理者', '02');
-- メールアドレス
INSERT INTO contents (community_id, contents_type_code, contents_value, open_level_code) VALUES (1, '02', null, '03');
-- 全体
INSERT INTO contents (community_id, contents_type_code, contents_value, open_level_code) VALUES (1, '00', null, '01');
-- マイフレンズ
INSERT INTO contents (community_id, contents_type_code, contents_value, open_level_code) VALUES (1, '11', null, '01');
-- マイフレンズコミュニティ
INSERT INTO community (community_id, community_type_code) VALUES (2, '20');
INSERT INTO sub_community (community_id, sub_community_id) VALUES (1, 2);
-- シーケンス番号の開始値を3にセット
SELECT setval ('community_id_seq', 3, false);

COMMIT;


----------------------------------------------------------------
--**************************************************************
-- カテゴリ情報
--**************************************************************
BEGIN;

-- カテゴリグループマスタ
COPY category_group_master FROM stdin;
0001	研究
0002	教育
0003	分野
0004	組織
0005	部活・サークル
0006	趣味・娯楽
0007	その他
\.

-- カテゴリマスタ
COPY category_master FROM stdin;
0001	研究室	0001
0002	研究グループ	0001
0003	勉強会	0001
0004	共同研究	0001
0005	プロジェクト	0001
0006	ゼミ	0001
0007	学会	0001
0008	シンポジウム	0001
0009	研究会	0001
0010	ワークショップ	0001
0011	講義	0002
0012	特別講義	0002
0013	生涯教育	0002
0014	ハードウェア	0003
0015	ソフトウェア	0003
0016	ネットワーク	0003
0017	データベース	0003
0018	アルゴリズム	0003
0019	音声処理	0003
0020	画像・映像処理	0003
0021	メディア処理	0003
0022	パターン情報処理	0003
0023	数理基礎	0003
0024	数理モデル	0003
0025	計算論	0003
0026	集積システム	0003
0027	論理回路	0003
0028	組込みシステム	0003
0029	オペレーティングシステム	0003
0030	システム制御	0003
0031	プログラミング	0003
0032	自然言語処理	0003
0033	セキュリティ	0003
0034	知識情報処理	0003
0035	生体情報処理	0003
0036	モバイル	0003
0037	ユビキタス	0003
0038	医療	0003
0039	福祉	0003
0040	コミュニケーション	0003
0041	グリッド	0003
0042	認知科学	0003
0043	メディア応用	0003
0044	シミュレーション	0003
0045	多自由度システム	0003
0046	物質情報	0003
0047	生命情報	0003
0048	大規模計算	0003
0049	医学一般	0003
0050	化学一般	0003
0051	生物学一般	0003
0052	物理学一般	0003
0053	情報モデル	0003
0054	分散協調	0003
0055	知的支援	0003
0056	情報デザイン	0003
0057	哲学・倫理	0003
0058	教養一般	0003
0059	言語	0003
0060	教育	0003
0061	心理	0003
0062	経済	0003
0063	文学	0003
0064	法律	0003
0065	社会	0003
0066	健康・保健	0003
0067	学部	0004
0068	学科	0004
0069	研究科	0004
0070	専攻	0004
0071	コース	0004
0072	委員会	0004
0073	文化系	0005
0074	運動系	0005
0075	本・作家	0006
0076	音楽・歌手	0006
0077	映画・テレビ・アニメ・俳優	0006
0078	ゲーム	0006
0079	車・バイク	0006
0080	旅行	0006
0081	インターネット・コンピュータ	0006
0082	フード・レストラン	0006
0083	ファッション	0006
0084	ペット・動物	0006
0085	スポーツ・アウトドア	0006
0086	資格・検定	0006
0087	趣味・娯楽全般	0006
0088	その他	0007
\.

COMMIT;


----------------------------------------------------------------


-- EOF
