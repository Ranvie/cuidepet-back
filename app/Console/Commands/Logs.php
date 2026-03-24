<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Logs extends Command {

  /**
   * Assinatura do comando
   * @var string
   */
  protected $signature = 'log:clear';

  /**
   * Descrição do comando
   * @var string
   */
  protected $description = 'Clear the output log file';

  /**
   * Executa o comando
   */
  public function handle() {
    $logFile = storage_path('logs/laravel.log');

    if (file_exists($logFile)) {
      file_put_contents($logFile, '');
    } else {
      $this->error('Error clearing log file.');
    }
  }
}
