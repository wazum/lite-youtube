CREATE TABLE sys_file_reference (
  tx_liteyoutuberenderer_autoload smallint DEFAULT 0 NOT NULL,
  tx_liteyoutuberenderer_no_cookie smallint DEFAULT 1 NOT NULL,
  tx_liteyoutuberenderer_short smallint DEFAULT 0 NOT NULL,
  tx_liteyoutuberenderer_show_title smallint DEFAULT 0 NOT NULL,
  tx_liteyoutuberenderer_poster_loading varchar(20) DEFAULT '' NOT NULL,
  tx_liteyoutuberenderer_playlist_id varchar(255) DEFAULT '' NOT NULL,
  tx_liteyoutuberenderer_video_start_at int DEFAULT 0 NOT NULL,
  tx_liteyoutuberenderer_param_controls smallint DEFAULT 1 NOT NULL,
  tx_liteyoutuberenderer_param_rel smallint DEFAULT 0 NOT NULL,
  tx_liteyoutuberenderer_param_loop smallint DEFAULT 0 NOT NULL,
  tx_liteyoutuberenderer_param_mute smallint DEFAULT 0 NOT NULL,
);

