<?php

    session_start();

    include_once("connection.php");
    include_once("url.php");

    $data = $_POST;    

    // MODIFICAÇÕES NO BANCO
    if(!empty($data)) {

        // print_r($data);

        // Criar contato
        if ($data["type"] === 'create') {
            
            $name = $data["name"];
            $phone = $data["phone"];
            $observations = $data["observations"];

            $query = "INSERT INTO contacts (name, phone, observations) VALUES (:name, :phone, :observations)";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":observations", $observations);

            try {

                $stmt->execute();
                $_SESSION["msg"] = "Contato criado com sucesso!";
        
            } catch(PDOException $err) {
                // erro na conexão
                $error = $err->getMessage();
                echo "Erro: $error";
            }   

        // Editar contato
        } else if ($data["type"] === "edit") {
                
            $name = $data["name"];
            $phone = $data["phone"];
            $observations = $data["observations"];
            $id = $data["id"];

            $query = "UPDATE contacts 
                        SET name = :name, phone = :phone, observations = :observations 
                        WHERE id = :id";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":observations", $observations);
            $stmt->bindParam(":id", $id);

            try {

                $stmt->execute();
                $_SESSION["msg"] = "Contato atualizado com sucesso!";
        
            } catch(PDOException $err) {
                // erro na conexão
                $error = $err->getMessage();
                echo "Erro: $error";
            }

        
        } else if ($data["type"] === "delete") {
           
            $id = $data["id"];

            $query = "DELETE FROM contacts WHERE id = :id";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":id", $id);

            try {

                $stmt->execute();
                $_SESSION["msg"] = "Contato removido com sucesso!";
        
            } catch(PDOException $err) {
                // erro na conexão
                $error = $err->getMessage();
                echo "Erro: $error";
            }

        #********
        } else if ($data["type"] !== "create" || $data["type"] !== "edit" || $data["type"] !== "delete") {
            echo "Ops, tente de novo";
            exit;
        }

        // Redirect HOME
        header("Location:" . $BASE_URL . "../index.php");

    // SELEÇÃO DE DADOS
    } else {

        $id;

        if (!empty($_GET)) {
            $id = $_GET["id"];
        }

        // Retorna o dado de um contato se houver get
        if (!empty($id)) {
            
            $query = "SELECT * FROM contacts WHERE id = :id";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":id", $id);

            $stmt->execute();

            $contact = $stmt->fetch();
            
        } else {
            // Retorna todos os contatos se o get for vazio
            $contacts = [];
        
            $query = "SELECT * FROM contacts";
        
            $stmt = $conn->prepare($query);
        
            // executar
            $stmt->execute();
        
            // executar todos os dados por meio do PDO
            $contacts = $stmt->fetchAll();        
        }

    }

    // FECHAR CONEXÃO PDO
    $conn = null;
