export default () => ({
    open: false, 
    errors: {},
    loading: false,

    async submit(form) {
        this.loading = true
        this.errors = {}

        const response = await fetch('/add_vehicle', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        })

        this.loading = false
        
        if (response.status === 422) {
            const data = await response.json()
            this.errors = data.errors
            return
        }

        if (response.ok) {
            this.open = false
            form.reset()
            window.location.reload() // or update table dynamically
        }
    },
})
