<?php

namespace Innmind\ProvisionerBundle\RabbitMQ;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Put data relative to a rabbitmq queue in an ENV variable
 */
class QueueHistory implements HistoryInterface
{
    protected $dir;
    protected $filesystem;
    protected $finder;
    protected $data = [];
    protected $length = 10;

    /**
     * Set the directory path where to store data
     *
     * @param string $dir
     */
    public function setStoreDirectory($dir)
    {
        $this->dir = (string) $dir;
    }

    /**
     * Set the filesystem
     *
     * @param Filesystem $fs
     */
    public function setFilesystem(Filesystem $fs)
    {
        $this->filesystem = $fs;
    }

    /**
     * Set the file finder
     *
     * @param Finder $finder
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Set the mex history length
     *
     * @param int $length
     */
    public function setHistoryLength($length)
    {
        $this->length = (int) $length;
    }

    /**
     * {@inheritdoc}
     */
    public function put($key, array $value)
    {
        $value = array_slice($value, -$this->length);
        $this->data[$this->sanitize($key)] = $value;

        $this->filesystem->dumpFile(
            $this->getPath($key),
            json_encode($value)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $path = $this->getPath($key);
        $dirname = dirname($path);

        if (isset($this->data[$this->sanitize($key)])) {
            return $this->data[$this->sanitize($key)];
        }

        if (!is_dir($dirname)) {
            $this->filesystem->mkdir($dirname);
        }

        $files = $this
            ->finder
            ->files()
            ->name(basename($path))
            ->in($dirname);

        if (count($files) === 0) {
            return [];
        }

        foreach ($files as $file) {
            $data = json_decode($file->getContents());
            $this->data[$this->sanitize($key)] = $data;
            return $data;
        }
    }

    /**
     * Build absolute path to the filename
     *
     * @param string $key
     *
     * @return string
     */
    protected function getPath($key)
    {
        return sprintf(
            '%s/%s.data',
            $this->dir,
            strtolower($this->sanitize((string) $key))
        );
    }

    /**
     * Transform any special character to an underscore
     *
     * @param string $text
     *
     * @return string
     */
    protected function sanitize($text)
    {
        return preg_replace('/[^a-zA-Z0-9]+/', '_', $text);
    }
}
