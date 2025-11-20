<x-app-layout title="Accessibility">
    <div class="max-w-4xl mx-auto space-y-8 py-8 text-slate-800">

        <h1 class="text-3xl font-bold">
            Accessibility on PROXIMA
        </h1>

        <p>
            PROXIMA aims to make its interfaces usable by as many people as possible,
            including users with disabilities. This page describes our current level of
            accessibility and the actions we are taking to improve it.
        </p>

        <section class="space-y-3">
            <h2 class="text-2xl font-semibold">Compliance and standards</h2>

            <p>
                PROXIMA is developed as a student project and is inspired by good practices
                from the <abbr title="Web Content Accessibility Guidelines">WCAG</abbr> 2.1
                and the French <abbr title="Référentiel général d’amélioration de l’accessibilité">RGAA</abbr> 4.0.
                The level of compliance is currently partial and is being improved
                progressively.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-2xl font-semibold">Accessibility limitations</h2>

            <p>
                Some parts of the application may not yet be fully accessible, for example:
            </p>

            <ul class="list-disc list-inside space-y-1">
                <li>Certain dynamic components may be difficult to use with a screen reader.</li>
                <li>Keyboard navigation may still be incomplete in some views.</li>
            </ul>

            <p>
                We are working to identify these issues and provide alternative solutions
                where possible.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-2xl font-semibold">Accessibility features</h2>

            <ul class="list-disc list-inside space-y-1">
                <li>Structured pages with headings to improve screen-reader navigation.</li>
                <li>Use of semantic HTML for forms, buttons and navigation elements.</li>
                <li>Consistent layout and components across the application.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-2xl font-semibold">Contact for accessibility issues</h2>

            <p>
                If you encounter an accessibility problem or have suggestions for improvement,
                you can contact the project owner:
            </p>

            <ul class="list-none space-y-1">
                <li>
                    Email:
                    <a href="mailto:daria.khanina@etu.unistra.fr"
                       class="text-primary-700 underline">
                        daria.khanina@etu.unistra.fr
                    </a>
                </li>
                <li>
                    Phone:
                    <a href="tel:+33624894858"
                       class="text-primary-700 underline">
                        +33 (0)6 24 89 48 58
                    </a>
                </li>
            </ul>

            <p>
                We will do our best to provide a useful answer and, when possible,
                a workaround or fix.
            </p>
        </section>

        {{-- Updates --}}
        <section class="space-y-3">
            <h2 class="text-2xl font-semibold">Updates</h2>

            <p>
                This accessibility statement will be updated as PROXIMA evolves and as
                improvements are implemented in the interface and underlying code.
            </p>

            <p class="text-sm text-slate-500">
                Last update: {{ now()->format('F j, Y') }}
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-2xl font-semibold">Additional resources</h2>

            <ul class="list-disc list-inside space-y-1">
                <li><a href="https://www.w3.org/WAI/standards-guidelines/wcag/"
                       class="text-primary-700 underline" target="_blank" rel="noopener">
                    WCAG 2.1 – Web Content Accessibility Guidelines (W3C)
                </a></li>
                <li><a href="https://accessibilite.numerique.gouv.fr/"
                       class="text-primary-700 underline" target="_blank" rel="noopener">
                    French RGAA resources
                </a></li>
            </ul>
        </section>
    </div>
</x-app-layout>
