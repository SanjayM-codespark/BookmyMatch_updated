<?= $this->extend('base'); ?>

<?= $this->section('pageCss'); ?>

<style>
    .delete-account-section {
        background: #10141E;
        padding: 40px 15px;
        min-height: 70vh;
        color: #ffffff;
    }

    .delete-account-box {
        max-width: 750px;
        margin: 0 auto;
        background: #191e2a;
        padding: 35px;
        border-radius: 10px;
    }

    .delete-account-box h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #ffffff;
    }

    .delete-account-box h4 {
        color: #ffffff;
        margin-top: 25px;
        margin-bottom: 10px;
    }

    .delete-account-box p {
        color: #c8ccd5;
        line-height: 1.7;
        margin-bottom: 15px;
    }

    .delete-account-box ol {
        color: #c8ccd5;
        line-height: 1.8;
        padding-left: 25px;
    }

    .delete-account-box li {
        margin-bottom: 12px;
    }

    .warning-box {
        background: #3a1f23;
        border: 1px solid #842029;
        color: #ffb3b3;
        padding: 15px;
        border-radius: 6px;
        margin: 20px 0;
    }

    .info-box {
        background: #172a3a;
        border: 1px solid #245c7a;
        color: #c8e7f5;
        padding: 15px;
        border-radius: 6px;
        margin-top: 20px;
    }
</style>

<?= $this->endSection(); ?>


<?= $this->section('main_content'); ?>

<section class="delete-account-section">

    <div class="container">

        <div class="delete-account-box">

            <h2>Delete Account</h2>

            <p>
                If you want to delete your account and associated personal
                data, please follow the account deletion steps below.
            </p>


            <h4>How to Delete Your Account</h4>

            <ol>

                <li>
                    <strong>Login to your account</strong><br>
                    Open the application and sign in using your registered
                    account.
                </li>

                <li>
                    <strong>Open Account Settings</strong><br>
                    Go to your <strong>Profile / Account</strong> section
                    and open <strong>Settings</strong>.
                </li>

                <li>
                    <strong>Select Delete Account</strong><br>
                    Select the <strong>Delete Account</strong> option from
                    the account settings.
                </li>

                <li>
                    <strong>Confirm Account Deletion</strong><br>
                    Review the account deletion information and confirm
                    that you want to delete your account.
                </li>

                <li>
                    <strong>Account Deletion Processing</strong><br>
                    Your account deletion request will be processed by
                    the application.
                </li>

                <li>
                    <strong>Data Deletion</strong><br>
                    Your account and eligible personal data associated
                    with the account will be deleted.
                </li>

                <li>
                    <strong>Deletion Completion</strong><br>
                    Once the deletion process is completed, you will no
                    longer be able to access the deleted account.
                </li>

            </ol>


            <div class="warning-box">

                <strong>Important:</strong><br>

                Account deletion is permanent. Once your account has been
                deleted, the account and eligible associated data may not
                be recoverable.

            </div>


            <h4>Data That Will Be Deleted</h4>

            <p>
                The following types of data associated with your account
                may be deleted as part of the account deletion process:
            </p>

            <ol>
                <li>Account information</li>
                <li>Profile information</li>
                <li>Personal information</li>
                <li>User-generated data associated with the account</li>
            </ol>


            <h4>Data Retention</h4>

            <p>
                Certain information may be retained where required by law,
                for security purposes, fraud prevention, dispute resolution,
                or other legitimate business requirements.
            </p>

            <div class="info-box">

                <strong>Account Deletion Request</strong><br>

                To request account deletion, follow the steps provided
                above through the application.

            </div>

        </div>

    </div>

</section>

<?= $this->endSection(); ?>


<?= $this->section('pageScript'); ?>

<?= $this->endSection(); ?>
