<?php

require 'controllers/Projects.php';

$projectsController = new Projects;
$projects = $projectsController->display();

?>
<html>
	<head>
		<title>Projects</title>
		<link rel="stylesheet" href="css/style.css">
	</head>

	<body>
		<div id="container">

			<div id="projects_container">
				<?php
				foreach ($projects as $project):
				?>
				<div class="project">
					<img src="<?php echo $project->image_url; ?>" alt="Project Image">
					<ul>
						<li><strong><?php echo $project->title; ?></strong></li>
						<li><?php echo $project->summary; ?></li>
						<li>Location: <?php echo $project->country; ?></li>
						<li>By <?php echo $project->organisation_name; ?></li>
						<li><a href="<?php echo $project->url; ?>">Go to organisation</a></li>
					</ul>
				</div>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>
		</div>
	</body>

</html>
