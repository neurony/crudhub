export default {
    methods: {
        copyToClipboard(text) {
            const textArea = document.createElement('textarea');

            document.body.appendChild(textArea);

            textArea.value = text;
            textArea.select();

            try {
                document.execCommand('copy');
            } catch (error) {
                console.error(error);
            } finally {
                document.body.removeChild(textArea);
            }
        }
    },
}
