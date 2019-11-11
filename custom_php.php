<?php
/*
 Template Name: Custom PHP
*/
?>

<?php
    function populate_RTF($vars, $doc_file) {

        $replacements = array (	'{'  => "\{",
                               '}'  => "\}");

        $document = file_get_contents($doc_file);
        if(!$document) {
            return false;
        }

        foreach($vars as $key=>$value) {
            $search = "%%".strtoupper($key)."%%";
            foreach($replacements as $orig => $replace) {
                $value = str_replace($orig, $replace, $value);
            }
            
            $document = str_replace($search, $value, $document);
        }
        
        return $document;
    }
if ($_POST['submit']) {

    $education = $_POST['education'];
    $job = $_POST['job'];
	$skills = $_POST['skill'];
    $cert = $_POST['cert']; 
    $extra = $_POST['extra'];

	
	
    //Personal details
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $suburb = $_POST['suburb'];
    $state = $_POST['state'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $objective = $_POST['objective'];
    
	
    //Header - name
    $name = $firstname." ".$lastname;
    $name = ucwords($name);

    //Header - state
    $state = ucwords($state);
    
    //Academic Qualification
    $educ_info = "";
	foreach ($education as $educ) {
		$educ_info .= '\trowd';
		$educ_info .= '\cellx3000';
		$educ_info .= '\cellx8000';
		$educ_info .= '\intbl '.$educ['Graduation_Year'].' \cell';
	    $educ_info .= '\intbl '.$educ['Degree'].' \cell';
		$educ_info .= '\row';
		$educ_info .= '\trowd';
		$educ_info .= '\cellx3000';
		$educ_info .= '\cellx8000';
		$educ_info .= '\intbl  \cell';
		$educ_info .= '\intbl '.$educ['Institution'].' - '.$educ['Location'].'\cell';
		$educ_info .= '\row';
	}

    //Work Experience
    $work_info = "";
    foreach ($job as $exp) {
		$work_info .= '\trowd';
		$work_info .= '\cellx3000';
		$work_info .= '\cellx8000';
		$work_info .= '\intbl '.$exp['start']. ' until '.$exp['end'].' \cell';
	    $work_info .= '\intbl '.$exp['title'].' - '.$exp['employer'].' ('.$exp['location'].') \cell';
		$work_info .= '\row';
		$work_info .= '\trowd';
		$work_info .= '\cellx3000';
		$work_info .= '\cellx8000';
		$work_info .= '\intbl  \cell';
		$work_info .= '\intbl '.$exp['description'].'\cell';
		$work_info .= '\row';
	}
    
    //Key Skill
    $skill_info = "";
	foreach ($skills as $s) {
		$skill_info .= '\trowd';
		$skill_info .= '\cellx1000';
	    $skill_info .= '\intbl '.$s.' \cell';
		$skill_info .= '\row';
	}
	
	//Certification
	$cert_info ="";
	foreach ($cert as $c) {
		$cert_info .= '\trowd';
		$cert_info .= '\cellx10000';
	    $cert_info .= '\intbl '.$c.' \cell';
		$cert_info .= '\row';
	}

    //Extra 
    $extra_info = "";
    foreach($extra as $e){
        if($e[type] == 'accomplishments'){
		$extra_info .= '\trowd';
		$extra_info .= '\cellx10000';
	    $extra_info .= '\intbl Accomplishments - '.$e[input].' \cell';
		$extra_info .= '\row';
        }
        if($e[type] == 'membership'){
		$extra_info .= '\trowd';
		$extra_info .= '\cellx10000';
	    $extra_info .= '\intbl Membership - '.$e[input].' \cell';
		$extra_info .= '\row';
        }
        if($e[type] == 'link'){
		$extra_info .= '\trowd';
		$extra_info .= '\cellx10000';
	    $extra_info .= '\intbl Link - '.$e[input].' \cell';
		$extra_info .= '\row';
        }
        if($e[type] == 'hobbies'){
		$extra_info .= '\trowd';
		$extra_info .= '\cellx10000';
	    $extra_info .= '\intbl Hobbies - '.$e[input].' \cell';
		$extra_info .= '\row';
        }
        if($e[type] == 'additional_infomation'){
		$extra_info .= '\trowd';
		$extra_info .= '\cellx10000';
	    $extra_info .= '\intbl Additional Information - '.$e[input].' \cell';
		$extra_info .= '\row';
        }
    }

	$vars = array('name' => $name,
	                'state' => $state,
	              'profile'     => $objective,
                  'academic' => $educ_info,
                  'qualification'  => $cert_info,
                  'career' => $work_info,
                  'keyskill' => $skill_info,
                  'extra'   => $extra_info);
		
    $new_rtf = populate_RTF($vars, "test.rtf");
    $fr = fopen('testOutput.rtf', 'w') ;

    fwrite($fr, $new_rtf);
    fclose($fr);

    $content = file_get_contents('testOutput.rtf');
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $filename = basename($file);
    
    // header
    $header = "From: test@example.com \r\n";
    $header .= "MIME-.Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    
    // message & attachment
    $nmessage = "--".$uid."\r\n";
    $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $nmessage .= "Name: ".$name.", Mobile Number: ".$phone.", Email: ".$email.",  Suburb: ".$suburb." , State: ".$state."\r\n\r\n";
    $nmessage .= "--".$uid."\r\n";
    $nmessage .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
    $nmessage .= "Content-Transfer-Encoding: base64\r\n";
    $nmessage .= "Content-Disposition: attachment; filename=MarlinCV.rtf\r\n\r\n";
    $nmessage .= $content."\r\n\r\n";
    $nmessage .= "--".$uid."--";

	/* 
	the  email and print attachment functions are in comment, please remove the comment operator to use it.
	for email function, please put on the email address you wan to receive .
	*/

	// For sending an email to admin
	/*
	mail("email address", "Marlin CV", $nmessage,$header);
 	if(mail){
            echo "<h1>Thank you! Your Marlin-CV has been sent successfully.</h1>";
		    }
    echo "<a href=''>back</a>";
	*/
	// For Downloading a RTF document
	//header('Content-type: application/ms-word');
    //header('Content-Disposition: attachment;Filename=.rtf');
    //echo $new_rtf;

} else {
get_header();
echo <<< test
<h1 style="color:red;">&nbsp&nbsp&nbsp&nbsp&nbspJoin Us Now! </h1>
<form action="" method="post"><b>
		<fieldset>
			<label for="firstname">First Name </label>
				<input type="text" name="firstname" id="firstname" required>
			<label for="lastname">Last Name </label>
				<input type="text" name="lastname" id="lastname">
				<br><br>
			
			<label for="suburb">Suburb </label>
				<input type="text" name="suburb" id="suburb">
			<label for="state">State </label>
				<input type="text" name="state" id="state"><br>
			<label for="email">Email Address </label>
				<input type="email" name="email" id="email">
			<label for="phone">Mobile Number </label>
				<input type="tel" name="phone" id="phone" pattern="[0-9]{10}" min="10" max="10">
			<br>
			<label for="statement">Professional Objective: </label>
			<textarea id="objective" name="objective" rows="4" cols="70" maxlength="600" placeholder="Briefly describe yourself in 100 words.(Aims, Objectives, your expectations from the organisation and why should you be hired.)" required></textarea>
		</fieldset>
		<fieldset>
			<legend>Academic Qualifications</legend>
			<button type="button" name="addEducation" id="addEducation"> Add Education + </button><br><br>
			<div for="Education" id="education_field" name="education_field">
					<label for="institution_name">Institution Name</label>
						<input type="text" size="20" name="education[0][Institution]" id="institution_name" placeholder="eg,Swinburne University of Technology">
					<label for="institution_location">Institution Location</label>
						<input type="text" name="education[0][Location]" id="institution_location" maxlength="100" placeholder="eg,Hawthorn,VIC"><br>
					<label for="degree">Degree</label>
						<input type="text" name="education[0][Degree]" id="degree" maxlength="100" placeholder="eg,Bachelor of Computer Sciences">
					<label for="graduation_year">Graduation Year</label>
						<input type="month" name="education[0][Graduation_Year]" id="graduation_year" placeholder="eg,January 2020">
			</div>
		</fieldset>
		<fieldset>
			<legend>Work History</legend>
			<button type="button" name="Work History" id="addWork"> Add Work History + </button><br><br>
			<div for="Work" id="work_field" name="work_field">
					<label for="job_title">Job Title</label>
						<input type="text" name="job[0][title]" id="job_title">
					<label for="employer">Employer</label>
						<input type="text" name="job[0][employer]" id="employer">
					<label for="job_location">Job Location</label>
						<input type="text" name="job[0][location]" id="job_location"><br>
					<label for="job_start">Start Date</label>
						<input type="date" name="job[0][start]" id="job_start">
					<label for="job_end">End Date</label>
						<input type="date" name="job[0][end]" id="job_end"><br>
					<label for="job_desc">Role description</label><br>
						<textarea name="job[0][description]" id="job_desc" rows="4" cols="70" maxlength="500" placeholder=" Briefly describe your role at your work place."></textarea>
			</div>
		</fieldset>
		<fieldset>
			<legend>Certifications/Licenses</legend>
			<button type="button" name="addCert" id="addCert"> Add Certificate + </button>
			<div id="cert_field" name="cert_field">
				<input name="cert[0]" maxlength="50" size="100" id="cert" placeholder="eg,Responsible Service of Alcohol(RSA), Australian Driving Licenses, Certificate of Protection Officer">
			</div>
		</fieldset>
		<fieldset>
			<legend>Key Skills</legend>
			<button type="button" name="addSkill" id="addSkill"> Add Skill + </button>
			<div for="skill" id="skill_field" name="skill_field">
				<input type="text" name="skill[0]"  maxlength="50" size="100" id="skill">
			</div>
		</fieldset>
		<fieldset>
			<legend>Extra </legend>
			<button type="button" name="addExtra" id="addExtra"> Add Extra + </button>
			<label>Do you have anything else to add in your Marlin CV? </label>
			<div name="extra_field" id="extra_field">
				<select id="extraId" name="extra[0][type]" onchange="extra()">
					<option value="none"> None</option>
					<option value="accomplishments"> Accomplishments</option>
					<option value="membership"> Memberships/Affiliations</option>
					<option value="link"> Links</option>
					<option value="hobbies">Hobbies</option>
					<option value="additional_information">Additional Information</option>
				</select>
				<p id="selected">
				<input type="text" name="extra[0][input]" id="extra" />
				</p>
			</div>
		</fieldset>
		<input type="submit" value="Submit" name="submit"></b>
	</form>
<script>
	jQuery(document).ready(function () {
			jQuery(document).on('click', '.delete', function (e) {
				e.preventDefault;
				jQuery(this).parent('div').remove();
			});
		
		    var a=0;
			jQuery('#addEducation').click(function () {
			    a++;
				jQuery('#education_field').append('<div><hr style="border-top: 1px solid #000066;"><label for="institution_name">Institution Name</label><input type="text" size="20" name="education['+a+'][Institution]" id="institution_name" placeholder="eg,Swinburne University of Technology"><label for="institution_location">Institution Location</label><input type="text" name="education['+a+'][Location]" id="institution_location" maxlength="100" placeholder="eg,Hawthorn,VIC"><br><label for="degree">Degree</label><input type="text" name="education['+a+'][Degree]" id="degree" maxlength="100" placeholder="eg,Bachelor of Computer Sciences"><label for="graduation_year">Graduation Year</label><input type="month" name="education['+a+'][Graduation_Year]" id="graduation_year" placeholder="eg,January 2020"><a href="#" class="delete">Delete</a></div>');
			});
			
			
			var x= 0;
			jQuery('#addWork').click(function () {
			    x++;
				jQuery('#work_field').append('<div><hr style="border-top: 1px solid #000066;"><label for="job_title">Job Title</label><input type="text" name="job['+x+'][title]" id="job_title"><label for="employer">Employer</label><input type="text" name="job['+x+'][employer]" id="employer"><label for="job_location">Job Location</label><input type="text" name="job['+x+'][location]" id="job_location"><br><label for="job_start">Start Date</label><input type="date" name="job['+x+'][start]" id="job_start"><label for="job_end">End Date</label><input type="date" name="job['+x+'][end]" id="job_end"><br><label for="job_desc">Role description</label><br><textarea name="job['+x+'][description]" id="job_desc" rows="4" cols="70" maxlength="500" placeholder=" Briefly describe your role at your work place."></textarea><a href="#" class="delete">Delete</a></div>');
			});
			
			var b =0;
			jQuery('#addCert').click(function () {
			    b++;
				jQuery('#cert_field').append('<div><input name="cert['+b+']" maxlength="50" size="100" id="cert" placeholder="eg,Responsible Service of Alcohol(RSA), Australian Driving Licenses, Certificate of Protection Officer"><a href="#" class="delete">Delete</a></div>');
			});
			
			var c=0;
			jQuery('#addSkill').click(function () {
			    c++;
				jQuery('#skill_field').append('<div><input type="text"  maxlength="50" size="100" name="skill['+c+']" id="skill"><a href="#" class="delete">Delete</a></div>');
			});
			
	        var d=0;
			jQuery('#addExtra').click(function () {
			    d++;
				jQuery('#extra_field').append('<div><select id="extraId" name="extra['+d+'][type]" onchange="extra()"><option value="none"> None</option><option value="accomplishments"> Accomplishments</option><option value="membership"> Memberships/Affiliations</option><option value="link"> Links</option><option value="hobbies">Hobbies</option><option value="additional_information">Additional Information</option></select><p id="selected"><input type="text" name="extra['+d+'][input]" id="extra" /></p><a href="#" class="delete">Delete</a></div>');
			});

		});
		
	</script>

test;
}
?>