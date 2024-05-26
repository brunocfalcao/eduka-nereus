<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        .social-icon {
            width: 24px;
            height: 24px;
            vertical-align: middle;
            margin: 0 5px;
        }
        p {
            line-height: 1.6;
            margin: 20px 0;
        }
        .button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        .sub-line {
            font-size: 12px;
            color: #555555;
            text-align: center;
            margin: 20px 0;
        }
        .image-text {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        .image-text img {
            max-width: 150px;
            height: auto;
            margin-right: 20px;
        }
        .image-text div {
            flex: 1;
        }
        @media (max-width: 600px) {
            .button {
                width: 100%;
                margin: 10px 0;
            }
            .button-group {
                flex-direction: column;
                gap: 10px;
            }
            .image-text {
                flex-direction: column;
                text-align: center;
            }
            .image-text img {
                margin: 0 0 10px 0;
            }
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px 0; background-color: #f4f4f4;">

<!-- Header Start -->
<div style="text-align: center; padding: 20px 0; background-color: #f4f4f4;">
    <img src="https://placehold.co/150x50" alt="Postmark" style="max-width: 100%; height: auto;">
</div>
<!-- Header End -->

<!-- Container Start -->
<div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border: 1px solid #e0e0e0;">

    <!-- Content Start -->
    <div style="padding: 20px; text-align: left;">
        <!-- Welcome Paragraph Start -->
        <p>Hi {name},</p>
        <!-- Welcome Paragraph End -->

        <!-- Main Paragraph Start -->
        <p>A request was received for Postmark along with your email address. If you think you received this message by mistake, you can ignore it or contact support.</p>
        <p>To reset your password for account, click the button below. Remember to use your username {username} to log in, which is different from your email address.</p>
        <!-- Main Paragraph End -->

        <!-- Centered Button Start -->
        <a href="{reset_link}" class="button">Reset Password</a>
        <!-- Centered Button End -->

        <p>Password reset links are valid for 60 minutes.</p>

        <!-- Two Centered Buttons Start -->
        <div class="button-group">
            <a href="{link1}" class="button" style="margin: 0;">Button 1</a>
            <a href="{link2}" class="button" style="margin: 0;">Button 2</a>
        </div>
        <!-- Two Centered Buttons End -->

        <!-- Sub-Line Start -->
        <div class="sub-line">This is a disclaimer or sub-line text in dark gray and smaller font.</div>
        <!-- Sub-Line End -->

        <!-- Image and Text Block Start -->
        <div class="image-text">
            <img src="https://placehold.co/150x100" alt="Video Thumbnail">
            <div>
                <p>This is a description of the video. You can include more details here to explain what the video is about and why it might be interesting to the reader.</p>
            </div>
        </div>
        <!-- Image and Text Block End -->

    </div>
    <!-- Content End -->

</div>
<!-- Container End -->

<!-- Footer Start -->
<div style="text-align: center; padding: 20px 0;">
    <p style="font-size: 12px; color: #555555;">
        Mastering Nova - <a href="https://masteringnova.com" style="color: #555555; text-decoration: none;">masteringnova.com</a>
    </p>
    <p>
        <a href="{twitter_link}" style="color: #555555; text-decoration: none;">
            <img src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/icons/twitter.svg" alt="Twitter" class="social-icon">
        </a>
        <a href="{github_link}" style="color: #555555; text-decoration: none;">
            <img src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/icons/github.svg" alt="GitHub" class="social-icon">
        </a>
    </p>
</div>
<!-- Footer End -->

</body>
</html>
