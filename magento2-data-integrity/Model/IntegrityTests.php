<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model;

class IntegrityTests
{
    /**
     * @var TestInterface[]
     */
    private array $tests;

    public function __construct(array $tests)
    {
        $this->tests = $tests;
    }

    /**
     * @return void
     */
    public function runAll()
    {
        foreach ($this->tests as $test) {
            $this->run($test);
        }
    }

    /**
     * @param TestInterface $test
     * @return void
     */
    public function run(TestInterface $test)
    {
        $test->run();
    }

    /**
     * @param string $code
     * @return bool|TestInterface
     */
    public function getTestByCode(string $code)
    {
        foreach ($this->tests as $test) {
            if ($test->getCode() === $code) {
                return $test;
            }
        }

        return false;
    }

}
