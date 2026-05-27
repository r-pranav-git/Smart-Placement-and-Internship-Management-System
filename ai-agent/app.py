from fastapi import FastAPI
from pydantic import BaseModel
import requests

app = FastAPI()

class Query(BaseModel):
    message: str

@app.post("/chat")
async def chat(query: Query):

    try:

        user_message = query.message.lower()

        # CUSTOM SMART RESPONSES

        if "placement" in user_message:

            reply = (
                "Practice aptitude, DSA, and communication skills regularly. "
                "Build strong projects and attend mock interviews."
            )

        elif "interview" in user_message:

            reply = (
                "Prepare HR and technical questions thoroughly. "
                "Improve confidence through mock interviews."
            )

        elif "resume" in user_message:

            reply = (
                "Keep your resume short and highlight skills, projects, "
                "internships, and achievements clearly."
            )

        elif "internship" in user_message:

            reply = (
                "Apply early for internships and improve technical skills "
                "through real-world projects."
            )

        else:

            # AI FALLBACK

            response = requests.post(

                "http://localhost:11434/api/generate",

                json={

                    "model": "tinyllama",

                    "prompt":
                    f"""
                    Give a short professional reply.

                    User:
                    {user_message}

                    Assistant:
                    """,

                    "stream": False,

                    "options": {
                        "num_predict": 20
                    }
                },

                timeout=20
            )

            result = response.json()

            reply = result["response"].strip()

            # CLEAN RESPONSE

            reply = reply.replace("\n", " ")

            words = reply.split()

            reply = " ".join(words[:20])

        return {
            "reply": reply
        }

    except Exception as e:

        return {
            "reply": str(e)
        }