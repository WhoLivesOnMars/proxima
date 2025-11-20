<x-app-layout title="Privacy Policy">
    <div class="max-w-4xl mx-auto py-8 text-slate-800 leading-relaxed">

        <h1 class="text-3xl font-bold mb-6">Privacy Policy</h1>

        <p class="mb-6">
            This Privacy Policy explains how PROXIMA collects, uses, stores, and protects
            your personal data in accordance with the General Data Protection Regulation (GDPR).
            By using this application, you agree to the practices described below.
        </p>

        <h2 class="text-2xl font-semibold mt-8 mb-3">1. Data controller</h2>
        <p>
            <strong>Application:</strong> PROXIMA - Task & Project Management Tool <br>
            <strong>Responsible:</strong> Daria Khanina <br>
            <strong>Email:</strong> daria.khanina@etu.unistra.fr <br>
            <strong>Phone:</strong> +33 (0)6 24 89 48 58
        </p>

        <h2 class="text-2xl font-semibold mt-8 mb-3">2. Data we collect</h2>
        <p>The following categories of personal data may be collected when you use PROXIMA:</p>

        <ul class="list-disc pl-6 mt-2">
            <li><strong>Identity data:</strong> first name, last name.</li>
            <li><strong>Contact data:</strong> email address.</li>
            <li><strong>Authentication data:</strong> account credentials, hashed passwords.</li>
            <li><strong>Project data:</strong> projects, sprints, tasks, assignments, activity logs.</li>
            <li><strong>Usage data:</strong> date of account creation, login history, app interactions.</li>
        </ul>

        <h2 class="text-2xl font-semibold mt-8 mb-3">3. How we use your data</h2>
        <p>Your personal data may be used for the following purposes:</p>

        <ul class="list-disc pl-6 mt-2">
            <li>Managing user accounts and authentication.</li>
            <li>Allowing users to create and collaborate on projects and tasks.</li>
            <li>Sending notifications related to task deadlines, updates, or project activity.</li>
            <li>Improving the application's performance and user experience.</li>
            <li>Ensuring platform security and preventing misuse.</li>
        </ul>

        <h2 class="text-2xl font-semibold mt-8 mb-3">4. Legal basis for processing</h2>
        <p>The processing of your personal data relies on one or more of the following legal bases:</p>

        <ul class="list-disc pl-6 mt-2">
            <li><strong>Contractual obligations:</strong> providing access to the PROXIMA platform.</li>
            <li><strong>Legitimate interest:</strong> improving and securing the application.</li>
            <li><strong>Consent:</strong> receiving email notifications (you may withdraw your consent at any time).</li>
        </ul>

        <h2 class="text-2xl font-semibold mt-8 mb-3">5. Data storage and security</h2>
        <p>
            Your data is stored on servers managed through Plesk (Université de Strasbourg).
            We implement industry-standard measures to protect your personal information from
            unauthorized access, alteration, or disclosure.
        </p>

        <p>
            Access to personal data is restricted to authorized individuals involved in the
            academic development and maintenance of the platform.
        </p>

        <h2 class="text-2xl font-semibold mt-8 mb-3">6. Data retention</h2>
        <p>
            Your personal data is kept for as long as necessary to provide the service or comply
            with legal obligations. If you delete your account, all personal data will be removed
            or anonymized within a reasonable timeframe.
        </p>

        <h2 class="text-2xl font-semibold mt-8 mb-3">7. Sharing of data</h2>
        <p>
            Your data is <strong>not sold</strong>, <strong>shared</strong>, or <strong>transferred</strong> to third parties,
            except in the following cases:
        </p>

        <ul class="list-disc pl-6 mt-2">
            <li>Legal obligation (e.g., court order).</li>
            <li>Technical service providers strictly necessary for hosting or maintenance.</li>
        </ul>

        <h2 class="text-2xl font-semibold mt-8 mb-3">8. Your rights under GDPR</h2>
        <p>You have the following rights regarding your personal data:</p>

        <ul class="list-disc pl-6 mt-2">
            <li><strong>Right of access</strong> - obtain a copy of the data stored about you.</li>
            <li><strong>Right to rectification</strong> - correct inaccurate or incomplete data.</li>
            <li><strong>Right to erasure</strong> - request the deletion of your data.</li>
            <li><strong>Right to restriction of processing</strong>.</li>
            <li><strong>Right to object</strong> to certain processing activities.</li>
            <li><strong>Right to data portability</strong> - receive your data in a structured format.</li>
        </ul>

        <p class="mt-2">
            To exercise any of these rights, please contact us at:
            <a class="underline" href="mailto:daria.khanina@etu.unistra.fr">daria.khanina@etu.unistra.fr</a>
        </p>

        <h2 class="text-2xl font-semibold mt-8 mb-3">9. Cookies</h2>
        <p>
            PROXIMA uses only essential cookies required for authentication and the operation of the platform.
            No advertising or tracking cookies are used.
        </p>

        <h2 class="text-2xl font-semibold mt-8 mb-3">10. Updates to this Policy</h2>
        <p>
            This Privacy Policy may be updated to reflect changes in legal requirements or application features.
            The latest update will always be indicated here.
        </p>

        <p class="text-sm text-slate-500 pt-4 mt-7 border-t">
            Last updated: {{ now()->format('F d, Y') }}
        </p>

    </div>
</x-app-layout>
