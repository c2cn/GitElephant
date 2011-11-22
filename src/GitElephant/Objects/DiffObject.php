namespace GitElephant\Objects;

use GitElephant\Utilities;
use GitElephant\Objects\DiffChunk;

    const MODE_INDEX = 'index';
    const MODE_MODE = 'mode';
    const MODE_NEW_FILE = 'new_file';
    const MODE_DELETED_FILE = 'deleted_file';

    private $origPath;
    private $destPath;
    private $mode;
    private $chunks;

    public function __construct($lines)
    {
        $this->chunks = array();

        $this->findPath($lines[0]);
        $this->findMode($lines[1]);

        if ($this->mode == self::MODE_INDEX) {
            $this->findChunks(array_slice($lines, 4));
        }
    }

    private function findChunks($lines)
    {
        $arrayChunks = Utilities::preg_split_array($lines, '/@@ -(\d+,\d+) \+(\d+,\d+) @@?(.*)/');
        foreach($arrayChunks as $chunkLines) {
            $this->chunks[] = new DiffChunk($chunkLines);
        }
    }

    private function findPath($line)
    {
        $matches = array();
        if (preg_match('/^diff --git SRC\/(.*) DST\/(.*)$/', $line, $matches)) {
            $this->origPath = $matches[1];
            $this->destPath = $matches[2];
        }
    }
    private function findMode($line)
    {
        if (preg_match('/^index (.*)\.\.(.*) (.*)$/', $line)) {
            $this->mode = self::MODE_INDEX;
        }
        if (preg_match('/^mode (.*)\.\.(.*) (.*)$/', $line)) {
            $this->mode = self::MODE_MODE;
        }
        if (preg_match('/^new file mode (.*)/', $line)) {
            $this->mode = self::MODE_NEW_FILE;
        }
        if (preg_match('/^deleted file mode (.*)/', $line)) {
            $this->mode = self::MODE_DELETED_FILE;
        }
    }