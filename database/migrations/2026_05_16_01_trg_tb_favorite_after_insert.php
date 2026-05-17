<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  
  public function up(): void {
    DB::unprepared('
      CREATE TRIGGER trg_tb_favorite_after_insert
      AFTER INSERT ON tb_favorite
      FOR EACH ROW
      BEGIN
        UPDATE tb_announcement
        SET favorited_count = (
          SELECT COUNT(*)
          FROM tb_favorite
          WHERE announcement_id = NEW.announcement_id
        )
        WHERE id = NEW.announcement_id;
      END
    ');
  }

  public function down(): void {
    DB::unprepared('DROP TRIGGER IF EXISTS trg_tb_favorite_after_insert');
  }
};
