
export default {
    middleware: [
        function ({ redirect, push }: any) {
            push('/restaurants')
            redirect('/restaurants')
        },
    ],
};
