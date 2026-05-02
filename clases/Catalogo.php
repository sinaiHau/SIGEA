<?php
class Catalogo {
    private $baseDatos;
    private $tabla;
    private $id_tabla;
    private $nombre;

    public function __construct($conexion, $tabla, $id_tabla, $nombre) {
        $this->baseDatos = $conexion;
        $this->tabla = $tabla;
        $this->id_tabla = $id_tabla;
        $this->nombre = $nombre;
    }

    // --- ESTA ES LA FUNCIÓN QUE TE FALTA ---
    public function existe($valor) {
        $sql = "SELECT * FROM {$this->tabla} WHERE {$this->nombre} = ?";
        $stmt = $this->baseDatos->prepare($sql);
        $stmt->bind_param("s", $valor);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0;
    }

    public function agregar($valor) {
        $sql = "INSERT INTO {$this->tabla} ({$this->nombre}) VALUES (?)";
        $stmt = $this->baseDatos->prepare($sql);
        $stmt->bind_param("s", $valor);
        return $stmt->execute();
    }

    public function listar() {
        return $this->baseDatos->query("SELECT * FROM {$this->tabla} ORDER BY {$this->nombre} ASC");
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE {$this->id_tabla} = ?";
        $stmt = $this->baseDatos->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>