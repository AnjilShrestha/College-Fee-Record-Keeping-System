<span class='search-row'>
            <label for="course">Course</label>
            <select name="course" id="course">
                <option value="">Select Course</option>
                <?php
                include_once './private/dbconfig.php';
                $select_course = "SELECT * FROM course_tb";
                $result_course = $connection->query($select_course);
                if ($result_course->num_rows > 0) {
                    while ($cour = $result_course->fetch_assoc()) {
                        ?>
                        <option value="<?php echo $cour['course_name']; ?>" <?php echo ($course == $cour['course_name']) ? 'selected' : ''; ?>><?php echo $cour['course_name']; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
            <span class='err-msg'><?php echo isset($err['course']) ? $err['course'] : ''; ?></span>
        </span>
        <span class='search-row'>
            <label for="batch">Batch</label>
            <select name="batch" id="batch">
                <option value="">Select Batch</option>
                <?php
                if (!empty($course)) {
                    $select_batch = "SELECT * FROM batch_tb b
                    INNER JOIN course_tb  c ON c.course_id=b.course_id
                    WHERE c.course_name='$course'";
                    $result_batch = $connection->query($select_batch);
                    if ($result_batch->num_rows > 0) {
                        while ($bat = $result_batch->fetch_assoc()) {
                            ?>
                            <option value="<?php echo $bat['batch_name']; ?>" <?php echo ($batch == $bat['batch_name']) ? 'selected' : ''; ?>><?php echo $bat['batch_name']; ?></option>
                            <?php
                        }
                    }
                }
                ?>
            </select>
            <span class='err-msg'><?php echo isset($err['batch']) ? $err['batch'] : ''; ?></span>
        </span>
<script src="jquery-3.7.1.min.js"></script>
<script>
        $(document).ready(function() {
            $('#course').change(function() {
                var selectedcourse = $(this).val();
                $.ajax('load_batch.php', {
                    data: {
                        'course': selectedcourse
                    },
                    dataType: 'text',
                    method: 'post',
                    success: function(response) {
                        $('#batch').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>