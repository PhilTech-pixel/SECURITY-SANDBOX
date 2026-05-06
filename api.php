<?php
header('Content-Type: application/json');

// Get the action from query parameters
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Read the JSON body
$jsonBody = file_get_contents('php://input');
$data = json_decode($jsonBody, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

// Database Connection function
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "security_demo";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        return null;
    }
}

switch ($action) {
    case 'html_encode':
        $raw_input = isset($data['input']) ? $data['input'] : '';
        // ENT_QUOTES encodes both single and double quotes
        $encoded = htmlspecialchars($raw_input, ENT_QUOTES, 'UTF-8');
        echo json_encode(['status' => 'success', 'encoded' => $encoded, 'raw' => $raw_input]);
        break;

    case 'sql_search':
        $search_term = isset($data['term']) ? $data['term'] : '';
        $query_type = isset($data['type']) ? $data['type'] : 'safe';
        $results = [];

        $conn = getDbConnection();
        if (!$conn) {
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed. Please run db_setup.php first.']);
            exit;
        }

        try {
            if ($query_type === 'raw') {
                $query = $search_term;
                // Since raw queries could be anything (including multiple statements or statements that don't return results),
                // we'll try to execute it as a query.
                $stmt = $conn->query($query);
                if ($stmt !== false) {
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                echo json_encode(['status' => 'success', 'results' => $results, 'query' => $query]);

            } elseif ($query_type === 'vulnerable') {
                // Vulnerable: Direct string concatenation
                $query = "SELECT id, name, category, price FROM products WHERE name = '" . $search_term . "'";
                $stmt = $conn->query($query);
                if ($stmt !== false) {
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                echo json_encode(['status' => 'success', 'results' => $results, 'query' => $query]);

            } else {
                // Safe: Prepared statements
                $query = "SELECT id, name, category, price FROM products WHERE name = :name";
                $stmt = $conn->prepare($query);
                $stmt->execute(['name' => $search_term]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode(['status' => 'success', 'results' => $results, 'query' => $query]);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'query' => $query ?? '']);
        }
        break;

    case 'subscribe':
        $email = isset($data['email']) ? trim($data['email']) : '';
        
        // 1. Server-Side Email Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
            exit;
        }

        $conn = getDbConnection();
        if (!$conn) {
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
            exit;
        }

        try {
            // Check if already subscribed
            $stmt = $conn->prepare("SELECT id FROM newsletter_members WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                echo json_encode(['status' => 'error', 'message' => 'Email already subscribed.']);
                exit;
            }

            // Insert new subscriber safely
            $stmt = $conn->prepare("INSERT INTO newsletter_members (email) VALUES (:email)");
            $stmt->execute(['email' => $email]);
            
            echo json_encode(['status' => 'success', 'message' => 'Successfully subscribed!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
