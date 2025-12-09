<?php
namespace Vendor\EasyFrameWork\Core\Master;


use Exception;

final class DRP
{
    private string $adr;     // Adresse UUID
    private object $values;  // Données JSON décodées
    private string $idField;//Nom du champ d'ID
    private bool $loaded = false;
    private string $table;
    private int|string $id;
    private \PDO $pdo;
    private static $data=[];

    public function __construct(string $table, string $id, \PDO $pdo)
    {
        $this->table = $table;
        $this->id = explode(":",$id)[1];
        $this->idField=explode(":",$id)[0];
        $this->pdo = $pdo;
        $this->adr = $this->generateAdr();
    }

    // Interdiction de clonage ou de copie
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Reconstruction de DRP interdite");
    }

    private function generateAdr(): string
    {
        // UUID v4 — adresse logique persistante
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

private function load(): void
{
    if ($this->loaded) return;

    $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->idField} = :id");
    $stmt->execute(['id' => $this->id]);
    $data = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$data) throw new Exception("Aucune donnée pour DRP : {$this->table}#{$this->id}");

    // On fige les données en tableau
    $values = json_decode(json_encode($data), true);
    $this->values = $this->deepFreeze($values);
    $this->loaded = true;
    self::$data[$this->adr]=$this->values;
}
public static function commit($file=null){
    if($file==null && session_status()!=2){
        throw new Exception("Aucune session active et aucun fichier pour séraliser");
    }
    if($file){
        if(!file_exists($file)){
            
        }
    }
}
private function deepFreeze(array $data): object
{
    // Crée un objet en lecture seule
    return new class($data) {
        private array $data;
        public function __construct(array $d) { $this->data = $d; }
        public function __get(string $name): mixed {
            if (!array_key_exists($name, $this->data)) {
                throw new \Exception("Champ inexistant : $name");
            }
            return $this->data[$name];
        }
        public function __set($n, $v): void {
            throw new \Exception("Données immuables : modification interdite");
        }
    };
}


    public function __get(string $name): mixed
    {
        if ($name === 'adr') return $this->adr;

        if ($name === 'values') {
            $this->load();
            return $this->values;
        }

        throw new \Exception("Propriété inexistante : {$name}");
    }

    public function __set(string $name, mixed $value): void
    {
        if($name=="values")
        throw new \Exception("DRP est en lecture seule");
    }
}
