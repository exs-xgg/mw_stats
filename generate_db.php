<?php



							$dbConnect = mysqli_connect("localhost","root","root");

							$query = "show databases like 'hf_%' as db";
							$result = $dbConnect->query($query);
							while ($row = $result->fetch_assoc()) {
								echo $row['Database (hf_%)'] . '<br>';
							}