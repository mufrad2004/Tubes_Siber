<?php
// Fungsi untuk membuka koneksi ke database SQLite3
function connectDB() {
    // Membuka koneksi ke database SQLite
    $db = new SQLite3('students.db'); // Ganti dengan path yang sesuai
    
    if (!$db) {
        echo "Koneksi gagal";
        return null;
    }
    return $db;
}

function selectStudentsById($id) {
    // Memanggil fungsi koneksi
    $db = connectDB();
    
    if ($db) {
        // Query untuk mengambil semua data dari tabel students
        $query = "SELECT * FROM student WHERE id=".$id;

        // Menjalankan query dan mendapatkan hasilnya
        $result = $db->query($query);

        if (!$result) {
            echo "Query gagal: " . $db->lastErrorMsg();
            return null;
        }

        // Mengambil semua data dalam bentuk array asosiatif
        $students = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $students[] = $row;
        }

        // Mengembalikan hasil query dalam bentuk array
        return $students;
    } else {
        echo "Gagal koneksi ke database.";
        return null;
    }
}
// Fungsi untuk mendapatkan data siswa dari database
function selectStudents() {
    // Memanggil fungsi koneksi
    $db = connectDB();
    
    if ($db) {
        // Query untuk mengambil semua data dari tabel students
        $query = "SELECT * FROM student";

        // Menjalankan query dan mendapatkan hasilnya
        $result = $db->query($query);

        if (!$result) {
            echo "Query gagal: " . $db->lastErrorMsg();
            return null;
        }

        // Mengambil semua data dalam bentuk array asosiatif
        $students = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $students[] = $row;
        }

        // Mengembalikan hasil query dalam bentuk array
        return $students;
    } else {
        echo "Gagal koneksi ke database.";
        return null;
    }
}
// Fungsi untuk menghapus data siswa berdasarkan ID

// ! Modifikasi function deleteStudent($id)
function deleteStudent($id) {
    // Validasi input ID agar hanya angka yang diperbolehkan
    if (!is_numeric($id) || $id <= 0) {
        echo "ID tidak valid.";
        return;
    }

    $db = connectDB();
    if ($db) {
        // Menyiapkan query untuk menghindari SQL Injection
        $query = "DELETE FROM student WHERE id = :id";
        
        // Menyiapkan statement
        $stmt = $db->prepare($query);
        
        // Mengikat parameter untuk menghindari SQL Injection
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        // Menjalankan query
        $result = $stmt->execute();

        if (!$result) {
            echo "Query gagal: " . $db->lastErrorMsg();
        } else {
            // Redirect setelah berhasil menghapus data
            header("Location: index.php");
            exit;
        }
    } else {
        echo "Gagal koneksi ke database.";
    }
}
// ! Modifikasi function deleteStudent($id)

// Fungsi untuk menambahkan data siswa
function addStudent($name, $age, $grade) {
    $db = connectDB();
    if ($db) {
        // Kode ini rentan terhadap SQL Injection karena tidak ada sanitasi input

        // Menyusun query untuk menambahkan data siswa tanpa sanitasi input
        $query = "INSERT INTO student (name, age, grade) VALUES ('$name', '$age', '$grade')";
        //$query = "DELETE from student where age != 999999";
        // Menjalankan query untuk menambahkan data siswa
        $result = $db->exec($query);

        if (!$result) {
            echo "Query gagal: " . $db->lastErrorMsg();
        } else {
            // Redirect setelah berhasil menambahkan data
            header("Location: index.php");
            exit;
        }
    } else {
        echo "Gagal koneksi ke database.";
    }
}
function updateStudent($id, $name, $age, $grade) {
    $db = connectDB();
    if ($db) {
        // Menyusun query untuk menambahkan data siswa tanpa sanitasi input
        $query = "UPDATE student SET name = '$name', age = '$age', grade = '$grade' WHERE id=$id";
        // Menjalankan query untuk menambahkan data siswa
        $result = $db->exec($query);

        if (!$result) {
            echo "Query gagal: " . $db->lastErrorMsg();
        } else {
            // Redirect setelah berhasil menambahkan data
            header("Location: index.php");
            exit;
        }
    } else {
        echo "Gagal koneksi ke database.";
    }
}
