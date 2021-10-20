<?php
  class QuestionType extends BaseModel { // see question_types in DB
    const TEXT          = 1;
    const RADIO         = 2;
    const NONE          = 3;
    const DROP          = 4;
    const DROP_REASONS  = 5;
  }