<?php

namespace hmmhmmmm\quest\database;

use hmmhmmmm\quest\Quest;

interface Database{

   public function getDatabaseName(): string;
   
   public function close(): void;
   
   public function reset(): void;
   
}