<?php
/**
 *
 */
class SampleController
{
    /**
     *
     */
    public function actionReturnsString()
    {
        return 'response';
    }

    /**
     *
     */
    public function __invoke()
    {
        return 'invoke';
    }
}