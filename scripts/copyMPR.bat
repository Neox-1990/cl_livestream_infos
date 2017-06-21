xcopy /y D:\Games\LFS\data\mpr\qual.mpr D:\DEV\cl_livestream_infos\scripts\qual.mpr*
xcopy /y D:\Games\LFS\data\mpr\race.mpr D:\DEV\cl_livestream_infos\scripts\race.mpr*
php quali_to_json.php qual.mpr
php race_to_json.php race.mpr
