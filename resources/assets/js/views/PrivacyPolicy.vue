<template>
    <v-container>
        <v-layout>
            <v-flex xs12>
                <h1>Privatlivspolitik</h1>
                <p>
                    Fortegnelse over behandling af personoplysninger.
                    Dato for seneste ajourføring af dokumentet: 14.10.2019
                </p>

                <p class="text-primary mb-0">
                    Den korte version:
                </p>
                <p class="mb-3">
                    Vi gemmer kun dine log-ind detaljer (medlemsnr/tlf/email og adgangskode) på din egen enhed (localStorage & cookies) og din adgangskode er kun gemt som hashværdi på vores servere. Du har mulighed, for at se hvilke data der ligger på vores servere omkring dig, samt slette data. Hver gang der bliver lavet forespørgsel på noget data bliver dine krypterede log-ind detaljer sendt til vores servere som dekryptere dem og logger dig ind på Flexybox-hjemmesiden og finder det data du forespørger. Forbindelsen mellem dig og vores servere er krypteret med SSL. Forbindelsen mellem Flexybox og vores servere er krypteret med SSL.
                    Hvis der skulle ske et datalæk vil vi foresøge at underette de involverede parter, men da vi ikke kender e-mails på brugere er det svært at målrette en sådan besked.
                </p>

                <faq v-for="(faq, index) in faqs" :key="index" :question="faq.question" :answer="faq.answer"></faq>

                <p class="font-weight-bold">PS: Kildekoden til denne hjemmeside kan ses <a href="https://github.com/rasmus0201/flexyfitness" target="_blank">her</a>.</p>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import EventBus from '../event-bus';

    export default {
        data() {
            return {
                faqs: [
                    {
                        question: 'Hvem har ansvaret for databeskyttelse for brug af App\'en?',
                        answer: 'Rasmus Sørensen, tlf: 31755650, mail: bundsgaard.rasmus@gmail.com'
                    },
                    {
                        question: 'Hvad er formålene med behandlingen?',
                        answer: {
                            wrapper: 'ul',
                            tag: 'li',
                            items: [
                                'At hente aktivitetskalender fra Flexybox.com, og derved samle information omkring holdtilmeldning på denne app.',
                                'At vise brugerens holdtilmeldinger',
                                'At cache (gemt version) behandlet data, for at kunne levere en hurtigere service.'
                            ]
                        }
                    },
                    {
                        question: 'Hvilke personoplysninger behandler vi?',
                        answer: {
                            wrapper: 'ul',
                            tag: 'li',
                            items: [
                                'Alm. personoplysninger: Navn, e-mail, telefon, medlemsnummer, adgangskode, holdtilmeldinger, aktivitetskalender',
                                'Oplysninger, der er tillagt en højere grad af beskyttelse: Ingen'
                            ]
                        }
                    },
                    {
                        question: 'Hvem behandler vi oplysninger om?',
                        answer: {
                            wrapper: 'ul',
                            tag: 'li',
                            items: [
                                'Medlemmer',
                                'Ansatte',
                                'Trænere',
                                'Frivillige',
                                'Andre som indgår i kalenderen fra Flexybox.com'
                            ]
                        }
                    },
                    {
                        question: 'Hvem videregives oplysningerne til?',
                        answer: 'Data videresendes og behandles af Flexybox.com'
                    },
                    {
                        question: 'Hvornår sletter vi personoplysninger?',
                        answer: 'Data opbevares i 3 måneder efter seneste log-ind og vil herefter blive slettet automatisk. Sletning kan også foretages af brugeren.'
                    },
                    {
                        question: 'Hvordan opbevarer vi personoplysninger?',
                        answer: {
                            wrapper: 'ul',
                            tag: 'li',
                            items: [
                                'Brugerens egen enhed (localStorage/cookies) - krypterede log-ind oplysninger til Flexybox.com.',
                                'Forbindelser til vores servere er krypteret med SSL',
                                'Alle personoplysninger når man bruger App\'en vil blive behandlet af en server indenfor EU.',
                                'Adgangskoder er på vores servere er opbevaret som hashværdier.'
                            ]
                        }
                    },
                    {
                        question: 'Hvad gør vi, hvis der sker et brud på persondatasikkerheden?',
                        answer: {
                            wrapper: 'ul',
                            tag: 'li',
                            items: [
                                'Hvis alle, eller nogle, af de registrerede oplysninger bliver stjålet, hacket eller på anden måde kompromitteret, kontakter vi vores hovedorganisation og drøfter eventuel anmeldelse til politiet og til Datatilsynet.',
                                'Der vil komme en notits inde i App\'en (efter log-in). Det vil også altid være muligt at gå til denne side og se om der er nogle nyheder/informationer.'
                            ]
                        }
                    },
                    {
                        question: 'Hvad kan vores IT-system, og har vi tænkt databeskyttelse ind i vores IT-systemer?',
                        answer: {
                            wrapper: 'ul',
                            tag: 'li',
                            items: [
                                'Sende data til Flexybox.com',
                                'Hente data fra Flexybox.com',
                                'Behandle data fra Flexybox.com',
                                'Vise data fra Flexybox.com',
                                'Gemme (cache) behandlet data i en begrænset periode efter henting fra Flexybox.com'
                            ]
                        }
                    }
                ]
            }
        },

        mounted() {
            EventBus.$on('refresh', this.refresh);
        },

        methods: {
            refresh() {
                EventBus.$emit('refresh-done');
            }
        }
    }
</script>
