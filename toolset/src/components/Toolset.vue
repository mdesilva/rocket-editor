<template>
    <div id="toolset">
        <h1> Editor </h1>
        <p> Click on an element to modify it </p>
        <textarea id="textTool" placeholder="Enter new content for element" v-model="internalTextToolContent"/>
        <div id="drillbits">
            <p v-if="pageLink"> Page updated ! Click <a :href="pageLink" target="_blank"> here </a> to view. </p>
            <div id="toolsetButtons">
                <button @click="$emit('saveElement')" color="success">Save</button>  
                <button @click="$emit('exportHtml')" color="primary"> Export </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Toolset",
    data: function() {
        return {
            internalTextToolContent: ""
        }
    },
    props: {
        pageLink: String,
        textToolContent: String
    },
    watch: {
        //Because we cannot mutate props directly, we need to set our own data object based initially on the prop that was passed.
        textToolContent: function(val) {
            this.internalTextToolContent = val;
        }
    }
}
</script>

<style scoped>
#toolset {
    position: fixed;
    margin-left: 3.125%;
}

#drillbits {
    position: relative;
    top: 30vh;
}

#toolsetButtons {
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
}

#textTool {
    width: 100%;
    height: 250px;
    border-radius: 10px;
    color: black;
    padding: 1rem;
    background-color: white;
}
</style>