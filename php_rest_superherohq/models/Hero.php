<?php

class Hero{

	//DB conn
	private $conn;
	private $table = 'super_hero';

	//Hero attributes
	public $id;
	public $real_name;
	public $hero_name;
	public $publisher;
	public $fad;
	public $abilities;
	public $teams;

	//Constructor ith DB
	public function __construct($db){
		$this->conn =$db;
	}

	//Get Heroes
	public function read(){
		//query
		$query = 'SELECT
				h.id,
				h.hero_name
			FROM
				'. $this->table. ' h
			ORDER BY
				h.hero_name ASC';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//execute query
		$stmt->execute();

		return $stmt;
	}

	public function readSingle(){
        //query
		$query = 'SELECT
				h.id,
				h.real_name,
				h.hero_name,
				h.publisher,
				h.fad
			FROM
				'. $this->table. ' h
			WHERE
				h.id = ?
			LIMIT 0,1';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//Bind id
		$stmt->bindParam(1, $this->id);

		//execute query
		$stmt->execute();


		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->id = $row['id'];
		$this->real_name = $row['real_name'];
		$this->hero_name = $row['hero_name'];
		$this->publisher = $row['publisher'];
		$this->fad = $row['fad'];

    }


	public function getAbilities(){
		//query
		$query = 'SELECT
				a.ability
			FROM
				abilities a, hero_abilities h
			WHERE
				a.id = h.ability_id AND h.hero_id = ?
			ORDER BY
				a.ability ASC';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//Bind id
		$stmt->bindParam(1, $this->id);

		//execute query
		$stmt->execute();


		$this -> abilities = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
            array_push($this->abilities, $ability);
        }

    }

	public function getAffiliations(){
		//query
		$query = 'SELECT
				t.name
			FROM
				team t, affiliation a
			WHERE
				t.id = a.team_id AND a.hero_id = ?
			ORDER BY
				t.name ASC';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//Bind id
		$stmt->bindParam(1, $this->id);

		//execute query
		$stmt->execute();


		$this -> teams = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
            array_push($this->teams, $name);
        }

    }

	public function searchHero($name){
        //query
		$query = 'SELECT
				h.id,
				h.hero_name
			FROM
				'. $this->table. ' h
			WHERE
				h.hero_name LIKE "%'.$name.'%"
			ORDER BY
				h.hero_name ASC';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//execute query
		$stmt->execute();

		return $stmt;
    }

	//Creat Hero
	public function create(){
        //query
		$query = 'INSERT INTO '.$this->table.'
			SET
				real_name = :real_name,
				hero_name = :hero_name,
				publisher = :publisher,
				fad = :fad
		';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//bind data
		$stmt->bindParam(':real_name', $this->real_name);
		$stmt->bindParam(':hero_name', $this->hero_name);
		$stmt->bindParam(':publisher', $this->publisher);
		$stmt->bindParam(':fad', $this->fad);

		//execute query
		if($stmt->execute()){
			$query2 = 'SELECT s.id
						FROM super_hero s
						WHERE s.real_name LIKE "'.$this->real_name.'" AND s.hero_name LIKE "'.$this->hero_name.'"
						LIMIT 0,1';

            //prepare statement
            $stmt2 = $this->conn->prepare($query2);

            //execute query
            $stmt2->execute();


            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            return true;
        }
		else{
			printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }

	public function update(){
        //query
		$query = 'UPDATE '.$this->table.'
			SET
				real_name = :real_name,
				hero_name = :hero_name,
				publisher = :publisher,
				fad = :fad
			WHERE
				id = :id';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//bind data
		$stmt->bindParam(':real_name', $this->real_name);
		$stmt->bindParam(':hero_name', $this->hero_name);
		$stmt->bindParam(':publisher', $this->publisher);
		$stmt->bindParam(':fad', $this->fad);
		$stmt->bindParam(':id', $this->id);

		//execute query
		if($stmt->execute()){
            return true;
        }
		else{
			printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }

	public function resetPowers(){
        $query = 'DELETE FROM hero_abilities
			WHERE hero_id = :id';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//bind data
		$stmt->bindParam(':id', $this->id);

		//execute query
		$stmt->execute();

		$this->givePowers();

    }

	public function givePowers(){
        foreach ($this->abilities as $ability) {
			$query = 'INSERT INTO abilities (abilities.ability)
						VALUES ("'.$ability.'")
						ON DUPLICATE KEY UPDATE abilities.ability = abilities.ability';

			//prepare statement
            $stmt = $this->conn->prepare($query);

			//execute query
            if($stmt->execute()){
                printf("Abilidade adicionada: %s.\n", $ability);

				$query2 = 'SELECT a.id
						FROM abilities a
						WHERE a.ability LIKE "'.$ability.'"
						LIMIT 0,1';

				//prepare statement
                $stmt2 = $this->conn->prepare($query2);

                //execute query
                $stmt2->execute();


                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                $a_id = $row['id'];

				$query3 = 'INSERT INTO hero_abilities
							(`hero_id`,`ability_id`)
							VALUES
							('.$this->id.','.$a_id.')';

				//prepare statement
                $stmt3 = $this->conn->prepare($query3);

                //execute query
                $stmt3->execute();
            }
            else{
                printf("Error: %s.\n", $ability);
            }
		}
    }

	public function resetAffiliations(){
        $query = 'DELETE FROM affiliation
			WHERE hero_id = :id';

		//prepare statement
		$stmt = $this->conn->prepare($query);

		//bind data
		$stmt->bindParam(':id', $this->id);

		//execute query
		$stmt->execute();

		$this->giveAffiliations();

    }

	public function giveAffiliations(){
        foreach ($this->teams as $team) {
			$query = 'INSERT INTO team (team.name)
						VALUES ("'.$team.'")
						ON DUPLICATE KEY UPDATE team.name = team.name';

			//prepare statement
            $stmt = $this->conn->prepare($query);

			//execute query
            if($stmt->execute()){
                printf("Equipa adicionada: %s.\n", $team);

				$query2 = 'SELECT t.id
						FROM team t
						WHERE t.name LIKE "'.$team.'"
						LIMIT 0,1';

				//prepare statement
                $stmt2 = $this->conn->prepare($query2);

                //execute query
                $stmt2->execute();


                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                $t_id = $row['id'];

				$query3 = 'INSERT INTO affiliation
							(`hero_id`,`team_id`)
							VALUES
							('.$this->id.','.$t_id.')';

				//prepare statement
                $stmt3 = $this->conn->prepare($query3);

                //execute query
                $stmt3->execute();
            }
            else{
                printf("Error: %s.\n", $team);
            }
		}
    }
}

?>